<?php
/**
 * アカウントを扱うサービスクラス
 *
 * @author   keita-nishimoto
 * @since    2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

use Domain\RequestEntity;
use Domain\ResponseEntity;
use Exceptions\DomainException;
use Factories\Account\ValueFactory;
use Factories\EntityFactory;

/**
 * Class AccountService
 *
 * @category laravel-api-sample
 * @package Domain\Account
 * @author keita-nishimoto
 * @since 2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class AccountService
{

    /**
     * アカウント作成
     *
     * @param RequestEntity $requestEntity
     * @return \Domain\ResponseEntity
     * @throws DomainException
     */
    public function create(RequestEntity $requestEntity): ResponseEntity
    {
        $requestParams = $requestEntity->getRequestParams();

        $email         = $requestParams['email'];
        $password      = $requestParams['password'];
        $emailVerified = 0;

        if (array_key_exists('email_verified', $requestParams) === true) {
            if ($requestParams['email_verified'] === 1) {
                $emailVerified = $requestParams['email_verified'];
            }
        }

        $emailValue = ValueFactory::createEmailValue(
            [
                'email'         => $email,
                'emailVerified' => $emailVerified,
            ]
        );

        if (EmailSpecification::canRegisterableEmail($emailValue) === false) {
            throw new DomainException(40000);
        }

        $passwordValue = ValueFactory::createPasswordValue(
            [
                'password' => $password,
            ]
        );

        $accountEntity = AccountSpecification::newAccountEntity();
        $accountEntity
            ->setEmailValue($emailValue)
            ->setPasswordValue($passwordValue)
            ->saveEmail()
            ->savePassword();

        $sub = $accountEntity->getSub();

        $locationFormat = '%s/v1/accounts/%s';
        $location = sprintf(
            $locationFormat,
            env('APP_URL'),
            $sub
        );

        $header = [
            'Location' => $location,
        ];

        $embedded = [
            'email'          => $emailValue->getEmail(),
            'email_verified' => $emailValue->getEmailVerified(),
            'password_hash'  => $accountEntity->getPasswordValue()->getPasswordHash(),
        ];

        $response = [
            'sub'            => $sub,
            'account_status' => $accountEntity->convertAccountStatusToString(),
            '_links' => [
                'self' => [
                    'href' => "/v1/accounts/$sub",
                ],
            ],
            '_embedded' => $embedded,
        ];

        $responseEntity = EntityFactory::createResponseEntity($requestEntity);

        $responseEntity->setApiResponse($response)
            ->setHttpStatusCode(201)
            ->setOptionalHeader($header)
            ->createSuccessResponse();

        return $responseEntity;
    }
}
