<?php

namespace Bazar\Support;

class Countries
{
    /**
     * Get all the African countries.
     *
     * @return array
     */
    public static function africa(): array
    {
        return [];
    }

    /**
     * Get all the Asian countries.
     *
     * @return array
     */
    public static function asia(): array
    {
        return [];
    }

    /**
     * Get all the European countries.
     *
     * @return array
     */
    public static function europe(): array
    {
        return [];
    }

    /**
     * Get all the North American countries.
     *
     * @return array
     */
    public static function northAmerica(): array
    {
        return [];
    }

    /**
     * Get all the North American countries.
     *
     * @return array
     */
    public static function southAmerica(): array
    {
        return [];
    }

    /**
     * Get all the Oceanian countries.
     *
     * @return array
     */
    public function oceania(): array
    {
        return [];
    }

    /**
     * Get the name of the given country.
     *
     * @param  string  $country
     * @return string
     */
    public static function name(string $country): string
    {
        $all = static::all();

        return $all[$country] ?? $country;
    }

    /**
     * Get all the countries.
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            'AF' => __('Afghanistan'),
            'AX' => __('Åland Islands'),
            'AL' => __('Albania'),
            'DZ' => __('Algeria'),
            'AS' => __('American Samoa'),
            'AD' => __('Andorra'),
            'AO' => __('Angola'),
            'AI' => __('Anguilla'),
            'AQ' => __('Antarctica'),
            'AG' => __('Antigua & Barbuda'),
            'AR' => __('Argentina'),
            'AM' => __('Armenia'),
            'AW' => __('Aruba'),
            'AU' => __('Australia'),
            'AT' => __('Austria'),
            'AZ' => __('Azerbaijan'),
            'BS' => __('Bahamas'),
            'BH' => __('Bahrain'),
            'BD' => __('Bangladesh'),
            'BB' => __('Barbados'),
            'BY' => __('Belarus'),
            'BE' => __('Belgium'),
            'BZ' => __('Belize'),
            'BJ' => __('Benin'),
            'BM' => __('Bermuda'),
            'BT' => __('Bhutan'),
            'BO' => __('Bolivia'),
            'BA' => __('Bosnia & Herzegovina'),
            'BW' => __('Botswana'),
            'BV' => __('Bouvet Island'),
            'BR' => __('Brazil'),
            'IO' => __('British Indian Ocean Territory'),
            'VG' => __('British Virgin Islands'),
            'BN' => __('Brunei'),
            'BG' => __('Bulgaria'),
            'BF' => __('Burkina Faso'),
            'BI' => __('Burundi'),
            'KH' => __('Cambodia'),
            'CM' => __('Cameroon'),
            'CA' => __('Canada'),
            'CV' => __('Cape Verde'),
            'BQ' => __('Caribbean Netherlands'),
            'KY' => __('Cayman Islands'),
            'CF' => __('Central African Republic'),
            'TD' => __('Chad'),
            'CL' => __('Chile'),
            'CN' => __('China'),
            'CX' => __('Christmas Island'),
            'CC' => __('Cocos (Keeling) Islands'),
            'CO' => __('Colombia'),
            'KM' => __('Comoros'),
            'CG' => __('Congo - Brazzaville'),
            'CD' => __('Congo - Kinshasa'),
            'CK' => __('Cook Islands'),
            'CR' => __('Costa Rica'),
            'CI' => __('Côte d’Ivoire'),
            'HR' => __('Croatia'),
            'CU' => __('Cuba'),
            'CW' => __('Curaçao'),
            'CY' => __('Cyprus'),
            'CZ' => __('Czechia'),
            'DK' => __('Denmark'),
            'DJ' => __('Djibouti'),
            'DM' => __('Dominica'),
            'DO' => __('Dominican Republic'),
            'EC' => __('Ecuador'),
            'EG' => __('Egypt'),
            'SV' => __('El Salvador'),
            'GQ' => __('Equatorial Guinea'),
            'ER' => __('Eritrea'),
            'EE' => __('Estonia'),
            'SZ' => __('Eswatini'),
            'ET' => __('Ethiopia'),
            'FK' => __('Falkland Islands'),
            'FO' => __('Faroe Islands'),
            'FJ' => __('Fiji'),
            'FI' => __('Finland'),
            'FR' => __('France'),
            'GF' => __('French Guiana'),
            'PF' => __('French Polynesia'),
            'TF' => __('French Southern Territories'),
            'GA' => __('Gabon'),
            'GM' => __('Gambia'),
            'GE' => __('Georgia'),
            'DE' => __('Germany'),
            'GH' => __('Ghana'),
            'GI' => __('Gibraltar'),
            'GR' => __('Greece'),
            'GL' => __('Greenland'),
            'GD' => __('Grenada'),
            'GP' => __('Guadeloupe'),
            'GU' => __('Guam'),
            'GT' => __('Guatemala'),
            'GG' => __('Guernsey'),
            'GN' => __('Guinea'),
            'GW' => __('Guinea-Bissau'),
            'GY' => __('Guyana'),
            'HT' => __('Haiti'),
            'HM' => __('Heard & McDonald Islands'),
            'HN' => __('Honduras'),
            'HK' => __('Hong Kong SAR China'),
            'HU' => __('Hungary'),
            'IS' => __('Iceland'),
            'IN' => __('India'),
            'ID' => __('Indonesia'),
            'IR' => __('Iran'),
            'IQ' => __('Iraq'),
            'IE' => __('Ireland'),
            'IM' => __('Isle of Man'),
            'IL' => __('Israel'),
            'IT' => __('Italy'),
            'JM' => __('Jamaica'),
            'JP' => __('Japan'),
            'JE' => __('Jersey'),
            'JO' => __('Jordan'),
            'KZ' => __('Kazakhstan'),
            'KE' => __('Kenya'),
            'KI' => __('Kiribati'),
            'KW' => __('Kuwait'),
            'KG' => __('Kyrgyzstan'),
            'LA' => __('Laos'),
            'LV' => __('Latvia'),
            'LB' => __('Lebanon'),
            'LS' => __('Lesotho'),
            'LR' => __('Liberia'),
            'LY' => __('Libya'),
            'LI' => __('Liechtenstein'),
            'LT' => __('Lithuania'),
            'LU' => __('Luxembourg'),
            'MO' => __('Macao SAR China'),
            'MG' => __('Madagascar'),
            'MW' => __('Malawi'),
            'MY' => __('Malaysia'),
            'MV' => __('Maldives'),
            'ML' => __('Mali'),
            'MT' => __('Malta'),
            'MH' => __('Marshall Islands'),
            'MQ' => __('Martinique'),
            'MR' => __('Mauritania'),
            'MU' => __('Mauritius'),
            'YT' => __('Mayotte'),
            'MX' => __('Mexico'),
            'FM' => __('Micronesia'),
            'MD' => __('Moldova'),
            'MC' => __('Monaco'),
            'MN' => __('Mongolia'),
            'ME' => __('Montenegro'),
            'MS' => __('Montserrat'),
            'MA' => __('Morocco'),
            'MZ' => __('Mozambique'),
            'MM' => __('Myanmar (Burma)'),
            'NA' => __('Namibia'),
            'NR' => __('Nauru'),
            'NP' => __('Nepal'),
            'NL' => __('Netherlands'),
            'NC' => __('New Caledonia'),
            'NZ' => __('New Zealand'),
            'NI' => __('Nicaragua'),
            'NE' => __('Niger'),
            'NG' => __('Nigeria'),
            'NU' => __('Niue'),
            'NF' => __('Norfolk Island'),
            'KP' => __('North Korea'),
            'MK' => __('North Macedonia'),
            'MP' => __('Northern Mariana Islands'),
            'NO' => __('Norway'),
            'OM' => __('Oman'),
            'PK' => __('Pakistan'),
            'PW' => __('Palau'),
            'PS' => __('Palestinian Territories'),
            'PA' => __('Panama'),
            'PG' => __('Papua New Guinea'),
            'PY' => __('Paraguay'),
            'PE' => __('Peru'),
            'PH' => __('Philippines'),
            'PN' => __('Pitcairn Islands'),
            'PL' => __('Poland'),
            'PT' => __('Portugal'),
            'PR' => __('Puerto Rico'),
            'QA' => __('Qatar'),
            'RE' => __('Réunion'),
            'RO' => __('Romania'),
            'RU' => __('Russia'),
            'RW' => __('Rwanda'),
            'WS' => __('Samoa'),
            'SM' => __('San Marino'),
            'ST' => __('São Tomé & Príncipe'),
            'SA' => __('Saudi Arabia'),
            'SN' => __('Senegal'),
            'RS' => __('Serbia'),
            'SC' => __('Seychelles'),
            'SL' => __('Sierra Leone'),
            'SG' => __('Singapore'),
            'SX' => __('Sint Maarten'),
            'SK' => __('Slovakia'),
            'SI' => __('Slovenia'),
            'SB' => __('Solomon Islands'),
            'SO' => __('Somalia'),
            'ZA' => __('South Africa'),
            'GS' => __('South Georgia & South Sandwich Islands'),
            'KR' => __('South Korea'),
            'SS' => __('South Sudan'),
            'ES' => __('Spain'),
            'LK' => __('Sri Lanka'),
            'BL' => __('St. Barthélemy'),
            'SH' => __('St. Helena'),
            'KN' => __('St. Kitts & Nevis'),
            'LC' => __('St. Lucia'),
            'MF' => __('St. Martin'),
            'PM' => __('St. Pierre & Miquelon'),
            'VC' => __('St. Vincent & Grenadines'),
            'SD' => __('Sudan'),
            'SR' => __('Suriname'),
            'SJ' => __('Svalbard & Jan Mayen'),
            'SE' => __('Sweden'),
            'CH' => __('Switzerland'),
            'SY' => __('Syria'),
            'TW' => __('Taiwan'),
            'TJ' => __('Tajikistan'),
            'TZ' => __('Tanzania'),
            'TH' => __('Thailand'),
            'TL' => __('Timor-Leste'),
            'TG' => __('Togo'),
            'TK' => __('Tokelau'),
            'TO' => __('Tonga'),
            'TT' => __('Trinidad & Tobago'),
            'TN' => __('Tunisia'),
            'TR' => __('Turkey'),
            'TM' => __('Turkmenistan'),
            'TC' => __('Turks & Caicos Islands'),
            'TV' => __('Tuvalu'),
            'UM' => __('U.S. Outlying Islands'),
            'VI' => __('U.S. Virgin Islands'),
            'UG' => __('Uganda'),
            'UA' => __('Ukraine'),
            'AE' => __('United Arab Emirates'),
            'GB' => __('United Kingdom'),
            'US' => __('United States'),
            'UY' => __('Uruguay'),
            'UZ' => __('Uzbekistan'),
            'VU' => __('Vanuatu'),
            'VA' => __('Vatican City'),
            'VE' => __('Venezuela'),
            'VN' => __('Vietnam'),
            'WF' => __('Wallis & Futuna'),
            'EH' => __('Western Sahara'),
            'YE' => __('Yemen'),
            'ZM' => __('Zambia'),
            'ZW' => __('Zimbabwe'),
        ];
    }
}
