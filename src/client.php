<?php
declare(strict_types = 1);

namespace Cehajicm\openexchangerates;

use DateTime;

use DateTime;

/**
 * PHP client for the OpenExchangeRates API.
 *
 * @version 1.0.0
 *
 * @link https://openexchangerates.org/
 */
class client
{
    /**
     * API base URL.
     */
    const ENDPOINT = 'https://openexchangerates.org/api';

    /**
     * API endpoint parameters.
     */

    private $app_id = null;
    private $base = null;
    private $symbols = null;
    private $from = null;
    private $to = null;
    private $value = null;
    private $date = null;
    private $start = null;
    private $end = null;
    private $show_alternative = null;
    private $prettyprint = null;

    /**
     * Constructor.
     *
     * @param string $app_id
     */
    public function __construct(string $app_id = null)
    {
        $this->app_id = $app_id;
    }


    /**
     * @param string $base
     * @return client
     */
    public function base(string $base): client
    {
        $this->base = $base;
        return $this;
    }


    /**
     * @param array $symbols
     * @return client
     */
    public function symbols(array $symbols): client
    {
        $this->symbols = implode(",", $symbols);
        return $this;
    }

    /**
     * @param DateTime $from
     * @return client
     */
    public function from(DateTime $from): client
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @param DateTime $to
     * @return client
     */
    public function to(DateTime $to): client
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @param float $amount
     * @return client
     */
    public function value(float $amount): client
    {
        $this->value = $amount;
        return $this;
    }

    /**
     * @param DateTime $date
     * @return client
     */
    public function date(DateTime $date): client
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @param DateTime $start
     * @return client
     */
    public function start(DateTime $start): client
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @param null $end
     * @return client
     */
    public function end($end): client
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @param null $show_alternative
     * @return client
     */
    public function show_alternative($show_alternative): client
    {
        $this->show_alternative = $show_alternative;
        return $this;
    }

    /**
     * @param null $prettyprint
     * @return client
     */
    public function prettyprint($prettyprint): client
    {
        $this->prettyprint = $prettyprint;
        return $this;
    }

    /**
     * Request the API's "latest" endpoint.
     *
     * @return array
     */
    public function latest(): array
    {
        return $this->request('/latest.json', [
            'base' => $this->base,
            'symbols' => $this->symbols,
        ]);
    }

    /**
     * Execute the API request.
     *
     * @param string $endpoint
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function request($endpoint, $parameters): array
    {
        $parameters['app_id'] = $this->app_id;
        $url = self::ENDPOINT . $endpoint . '?' . urldecode(http_build_query($parameters));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($json, true);

        if (array_key_exists('error', $response)) {
            throw new \InvalidArgumentException($response['status'] . ' - ' . $response['description']);
        }

        return $response;
    }

    /**
     * Request the API's "historical" endpoint.
     *
     * @return array
     */
    public function historical(): array
    {
        if (!($this->date instanceof DateTime)) {
            throw new \InvalidArgumentException('You need to set DATE to get historical information.');
        }
        return $this->request('/historical/' . $this->date->format('Y-m-d') . '.json', [
            'base' => $this->base,
            'symbols' => $this->symbols,
        ]);
    }

    /**
     * Request the API's "currencies" endpoint.
     *
     * @return array
     */
    public function currencies(): array
    {
        return $this->request('/currencies.json', [
            'prettyprint' => $this->prettyprint,
            'show_alternative' => $this->show_alternative,
        ]);
    }

    /**
     * Request the API's "timeseries" endpoint.
     *
     * @return array
     */
    public function timeseries(): array
    {
        return $this->request('/time-series.json', [
            'start' => $this->start,
            'end' => $this->end,
            'symbols' => $this->symbols,
            'base' => $this->base,
            'prettyprint' => $this->prettyprint,
        ]);
    }

    /**
     * Request the API's "convert" endpoint.
     *
     * @return array
     */
    public function convert(): array
    {
        if (!($this->from instanceof DateTime)) {
            throw new \InvalidArgumentException('You need to set FROM date to convert money value from one currency to another.');
        }

        if (!($this->to instanceof DateTime)) {
            throw new \InvalidArgumentException('You need to set TO date to convert money value from one currency to another.');
        }

        if (!is_float($this->value)) {
            throw new \InvalidArgumentException('You need to set VALUE to convert money value from one currency to another.');
        }


        return $this->request('/convert/' . strval($this->value) . '/' . $this->from->format('Y-m-d') . '/' . $this->to->format('Y-m-d'), [
            'prettyprint' => $this->prettyprint,
        ]);
    }

    /**
     * Request the API's "usage" endpoint.
     *
     * @return array
     */
    public function usage(): array
    {
        return $this->request('/usage.json', [
            'prettyprint' => $this->prettyprint,
        ]);
    }


}
