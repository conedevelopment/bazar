<?php

namespace Cone\Bazar\Support;

abstract class Countries
{
    /**
     * Get all the African countries.
     *
     * @return array
     */
    public static function africa(): array
    {
        return [
            __('Algeria') => 'DZ',
            __('Angola') => 'AO',
            __('Botswana') => 'BW',
            __('Burundi') => 'BI',
            __('Cameroon') => 'CM',
            __('Cape Verde') => 'CV',
            __('Central African Republic') => 'CF',
            __('Chad') => 'TD',
            __('Comoros') => 'KM',
            __('Mayotte') => 'YT',
            __('Congo - Brazzaville') => 'CG',
            __('Congo - Kinshasa') => 'CD',
            __('Benin') => 'BJ',
            __('Equatorial Guinea') => 'GQ',
            __('Ethiopia') => 'ET',
            __('Eritrea') => 'ER',
            __('Djibouti') => 'DJ',
            __('Gabon') => 'GA',
            __('Gambia') => 'GM',
            __('Ghana') => 'GH',
            __('Guinea') => 'GN',
            __('Côte d’Ivoire') => 'CI',
            __('Kenya') => 'KE',
            __('Lesotho') => 'LS',
            __('Liberia') => 'LR',
            __('Libya') => 'LY',
            __('Madagascar') => 'MG',
            __('Malawi') => 'MW',
            __('Mali') => 'ML',
            __('Mauritania') => 'MR',
            __('Mauritius') => 'MU',
            __('Morocco') => 'MA',
            __('Mozambique') => 'MZ',
            __('Namibia') => 'NA',
            __('Niger') => 'NE',
            __('Nigeria') => 'NG',
            __('Guinea-Bissau') => 'GW',
            __('Réunion') => 'RE',
            __('Rwanda') => 'RW',
            __('St. Helena') => 'SH',
            __('São Tomé and Príncipe') => 'ST',
            __('Senegal') => 'SN',
            __('Seychelles') => 'SC',
            __('Sierra Leone') => 'SL',
            __('Somalia') => 'SO',
            __('South Africa') => 'ZA',
            __('Zimbabwe') => 'ZW',
            __('South Sudan') => 'SS',
            __('Western Sahara') => 'EH',
            __('Sudan') => 'SD',
            __('Eswatini') => 'SZ',
            __('Togo') => 'TG',
            __('Tunisia') => 'TN',
            __('Uganda') => 'UG',
            __('Egypt') => 'EG',
            __('Tanzania') => 'TZ',
            __('Burkina Faso') => 'BF',
            __('Zambia') => 'ZM',
        ];
    }

    /**
     * Get all the Anctarctican countries.
     *
     * @return array
     */
    public static function antarctica(): array
    {
        return [
            __('Antarctica') => 'AQ',
            __('Bouvet Island') => 'BV',
            __('South Georgia and South Sandwich Islands') => 'GS',
            __('French Southern Territories') => 'TF',
            __('Heard and McDonald Islands') => 'HM',
        ];
    }

    /**
     * Get all the Asian countries.
     *
     * @return array
     */
    public static function asia(): array
    {
        return [
            __('Afghanistan') => 'AF',
            __('Azerbaijan') => 'AZ',
            __('Bahrain') => 'BH',
            __('Bangladesh') => 'BD',
            __('Armenia') => 'AM',
            __('Bhutan') => 'BT',
            __('British Indian Ocean Territory') => 'IO',
            __('Brunei') => 'BN',
            __('Myanmar (Burma)') => 'MM',
            __('Cambodia') => 'KH',
            __('Sri Lanka') => 'LK',
            __('China') => 'CN',
            __('Taiwan') => 'TW',
            __('Christmas Island') => 'CX',
            __('Cocos (Keeling) Islands') => 'CC',
            __('Georgia') => 'GE',
            __('Palestinian Territories') => 'PS',
            __('Hong Kong SAR China') => 'HK',
            __('India') => 'IN',
            __('Indonesia') => 'ID',
            __('Iran') => 'IR',
            __('Iraq') => 'IQ',
            __('Israel') => 'IL',
            __('Japan') => 'JP',
            __('Kazakhstan') => 'KZ',
            __('Jordan') => 'JO',
            __('North Korea') => 'KP',
            __('South Korea') => 'KR',
            __('Kuwait') => 'KW',
            __('Kyrgyzstan') => 'KG',
            __('Laos') => 'LA',
            __('Lebanon') => 'LB',
            __('Macao SAR China') => 'MO',
            __('Malaysia') => 'MY',
            __('Maldives') => 'MV',
            __('Mongolia') => 'MN',
            __('Oman') => 'OM',
            __('Nepal') => 'NP',
            __('Pakistan') => 'PK',
            __('Philippines') => 'PH',
            __('Timor-Leste') => 'TL',
            __('Qatar') => 'QA',
            __('Russia') => 'RU',
            __('Saudi Arabia') => 'SA',
            __('Singapore') => 'SG',
            __('Vietnam') => 'VN',
            __('Syria') => 'SY',
            __('Tajikistan') => 'TJ',
            __('Thailand') => 'TH',
            __('United Arab Emirates') => 'AE',
            __('Turkey') => 'TR',
            __('Turkmenistan') => 'TM',
            __('Uzbekistan') => 'UZ',
            __('Yemen') => 'YE',
        ];
    }

    /**
     * Get all the European countries.
     *
     * @return array
     */
    public static function europe(): array
    {
        return [
            __('Albania') => 'AL',
            __('Andorra') => 'AD',
            __('Austria') => 'AT',
            __('Belgium') => 'BE',
            __('Bosnia and Herzegovina') => 'BA',
            __('Bulgaria') => 'BG',
            __('Belarus') => 'BY',
            __('Croatia') => 'HR',
            __('Cyprus') => 'CY',
            __('Czechia') => 'CZ',
            __('Denmark') => 'DK',
            __('Estonia') => 'EE',
            __('Faroe Islands') => 'FO',
            __('Finland') => 'FI',
            __('Åland Islands') => 'AX',
            __('France') => 'FR',
            __('Germany') => 'DE',
            __('Gibraltar') => 'GI',
            __('Greece') => 'GR',
            __('Vatican City') => 'VA',
            __('Hungary') => 'HU',
            __('Iceland') => 'IS',
            __('Ireland') => 'IE',
            __('Italy') => 'IT',
            __('Latvia') => 'LV',
            __('Liechtenstein') => 'LI',
            __('Lithuania') => 'LT',
            __('Luxembourg') => 'LU',
            __('Malta') => 'MT',
            __('Monaco') => 'MC',
            __('Moldova') => 'MD',
            __('Montenegro') => 'ME',
            __('Netherlands') => 'NL',
            __('Norway') => 'NO',
            __('Poland') => 'PL',
            __('Portugal') => 'PT',
            __('Romania') => 'RO',
            __('San Marino') => 'SM',
            __('Serbia') => 'RS',
            __('Slovakia') => 'SK',
            __('Slovenia') => 'SI',
            __('Spain') => 'ES',
            __('Svalbard and Jan Mayen') => 'SJ',
            __('Sweden') => 'SE',
            __('Switzerland') => 'CH',
            __('Ukraine') => 'UA',
            __('North Macedonia') => 'MK',
            __('United Kingdom') => 'GB',
            __('Guernsey') => 'GG',
            __('Jersey') => 'JE',
            __('Isle of Man') => 'IM',
        ];
    }

    /**
     * Get all the North American countries.
     *
     * @return array
     */
    public static function northAmerica(): array
    {
        return [
            __('Antigua and Barbuda') => 'AG',
            __('Bahamas') => 'BS',
            __('Barbados') => 'BB',
            __('Bermuda') => 'BM',
            __('Belize') => 'BZ',
            __('British Virgin Islands') => 'VG',
            __('Canada') => 'CA',
            __('Cayman Islands') => 'KY',
            __('Costa Rica') => 'CR',
            __('Cuba') => 'CU',
            __('Dominica') => 'DM',
            __('Dominican Republic') => 'DO',
            __('El Salvador') => 'SV',
            __('Greenland') => 'GL',
            __('Grenada') => 'GD',
            __('Guadeloupe') => 'GP',
            __('Guatemala') => 'GT',
            __('Haiti') => 'HT',
            __('Honduras') => 'HN',
            __('Jamaica') => 'JM',
            __('Martinique') => 'MQ',
            __('Mexico') => 'MX',
            __('Montserrat') => 'MS',
            __('Curaçao') => 'CW',
            __('Aruba') => 'AW',
            __('Sint Maarten') => 'SX',
            __('Caribbean Netherlands') => 'BQ',
            __('Nicaragua') => 'NI',
            __('Panama') => 'PA',
            __('Puerto Rico') => 'PR',
            __('St. Barthélemy') => 'BL',
            __('St. Kitts and Nevis') => 'KN',
            __('Anguilla') => 'AI',
            __('St. Lucia') => 'LC',
            __('St. Martin') => 'MF',
            __('St. Pierre and Miquelon') => 'PM',
            __('St. Vincent and Grenadines') => 'VC',
            __('Trinidad and Tobago') => 'TT',
            __('Turks and Caicos Islands') => 'TC',
            __('United States') => 'US',
            __('U.S. Virgin Islands') => 'VI',
        ];
    }

    /**
     * Get all the South American countries.
     *
     * @return array
     */
    public static function southAmerica(): array
    {
        return [
            __('Argentina') => 'AR',
            __('Bolivia') => 'BO',
            __('Brazil') => 'BR',
            __('Chile') => 'CL',
            __('Colombia') => 'CO',
            __('Ecuador') => 'EC',
            __('Falkland Islands') => 'FK',
            __('French Guiana') => 'GF',
            __('Guyana') => 'GY',
            __('Paraguay') => 'PY',
            __('Peru') => 'PE',
            __('Suriname') => 'SR',
            __('Uruguay') => 'UY',
            __('Venezuela') => 'VE',
        ];
    }

    /**
     * Get all the Oceanian countries.
     *
     * @return array
     */
    public static function oceania(): array
    {
        return [
            __('American Samoa') => 'AS',
            __('Australia') => 'AU',
            __('Solomon Islands') => 'SB',
            __('Cook Islands') => 'CK',
            __('Fiji') => 'FJ',
            __('French Polynesia') => 'PF',
            __('Kiribati') => 'KI',
            __('Guam') => 'GU',
            __('Nauru') => 'NR',
            __('New Caledonia') => 'NC',
            __('Vanuatu') => 'VU',
            __('New Zealand') => 'NZ',
            __('Niue') => 'NU',
            __('Norfolk Island') => 'NF',
            __('Northern Mariana Islands') => 'MP',
            __('U.S. Outlying Islands') => 'UM',
            __('Micronesia') => 'FM',
            __('Marshall Islands') => 'MH',
            __('Palau') => 'PW',
            __('Papua New Guinea') => 'PG',
            __('Pitcairn Islands') => 'PN',
            __('Tokelau') => 'TK',
            __('Tonga') => 'TO',
            __('Tuvalu') => 'TV',
            __('Wallis and Futuna') => 'WF',
            __('Samoa') => 'WS',
        ];
    }

    /**
     * Get the name of the given country.
     *
     * @param  string  $country
     * @return string
     */
    public static function name(string $country): string
    {
        return array_search($country, static::all()) ?: $country;
    }

    /**
     * Get all the countries.
     *
     * @return array
     */
    public static function all(): array
    {
        $countries = array_merge(
            static::africa(),
            static::antarctica(),
            static::asia(),
            static::europe(),
            static::northAmerica(),
            static::southAmerica(),
            static::oceania()
        );

        asort($countries);

        return $countries;
    }
}
