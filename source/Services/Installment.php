<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PagSeguro\Services;

use PagSeguro\Domains\Account\Credentials;
use PagSeguro\Enum\Properties\Current;
use PagSeguro\Helpers\Currency;
use PagSeguro\Parsers\Installment\Request;
use PagSeguro\Resources\Connection;
use PagSeguro\Resources\Http;
use PagSeguro\Resources\Responsibility;

/**
 * Description of Installment
 *
 */
class Installment
{
    /**
     * @param Credentials $credentials
     * @param mixed $params
     * @return Pagseguro\Domains\Responses\Installments
     * @throws \Exception
     */
    public static function create(Credentials $credentials, $params)
    {
        try {
            $connection = new Connection\Data($credentials);
            $http = new Http();
            $http->get(self::request($connection, $params));

            return Responsibility::http($http, new Request());
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
    
    /**
     * Build the service request url
     * @param \PagSeguro\Resources\Connection\Data $connection
     * @param mixed $params
     * @return string
     */
    private static function request(Connection\Data $connection, $params)
    {
        return sprintf(
            "%1s?%2s%3s%4s%5s",
            $connection->buildInstallmentRequestUrl(),
            $connection->buildCredentialsQuery(),
            sprintf("&%s=%s", Current::INSTALLMENT_AMOUNT, Currency::toDecimal($params['amount'])),
            !isset($params['card_brand']) ?: sprintf("&%s=%s", Current::INSTALLMENT_CARD_BRAND, $params['card_brand']),
            !isset($params['max_installment_no_interest']) ?: sprintf("&%s=%s", Current::INSTALLMENT_MAX_INSTALLMENT_NO_INTEREST, $params['max_installment_no_interest'])
        );
    }
}
