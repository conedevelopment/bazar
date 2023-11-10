<?php

namespace Cone\Bazar\Support;

abstract class Countries
{
    /**
     * Get all the African countries.
     */
    public static function africa(): array
    {
        return [
            'DZ' => __('Algeria'),
            'AO' => __('Angola'),
            'BW' => __('Botswana'),
            'BI' => __('Burundi'),
            'CM' => __('Cameroon'),
            'CV' => __('Cape Verde'),
            'CF' => __('Central African Republic'),
            'TD' => __('Chad'),
            'KM' => __('Comoros'),
            'YT' => __('Mayotte'),
            'CG' => __('Congo - Brazzaville'),
            'CD' => __('Congo - Kinshasa'),
            'BJ' => __('Benin'),
            'GQ' => __('Equatorial Guinea'),
            'ET' => __('Ethiopia'),
            'ER' => __('Eritrea'),
            'DJ' => __('Djibouti'),
            'GA' => __('Gabon'),
            'GM' => __('Gambia'),
            'GH' => __('Ghana'),
            'GN' => __('Guinea'),
            'CI' => __('Côte d\'Ivoire'),
            'KE' => __('Kenya'),
            'LS' => __('Lesotho'),
            'LR' => __('Liberia'),
            'LY' => __('Libya'),
            'MG' => __('Madagascar'),
            'MW' => __('Malawi'),
            'ML' => __('Mali'),
            'MR' => __('Mauritania'),
            'MU' => __('Mauritius'),
            'MA' => __('Morocco'),
            'MZ' => __('Mozambique'),
            'NA' => __('Namibia'),
            'NE' => __('Niger'),
            'NG' => __('Nigeria'),
            'GW' => __('Guinea-Bissau'),
            'RE' => __('Réunion'),
            'RW' => __('Rwanda'),
            'SH' => __('St. Helena'),
            'ST' => __('São Tomé and Príncipe'),
            'SN' => __('Senegal'),
            'SC' => __('Seychelles'),
            'SL' => __('Sierra Leone'),
            'SO' => __('Somalia'),
            'ZA' => __('South Africa'),
            'ZW' => __('Zimbabwe'),
            'SS' => __('South Sudan'),
            'EH' => __('Western Sahara'),
            'SD' => __('Sudan'),
            'SZ' => __('Eswatini'),
            'TG' => __('Togo'),
            'TN' => __('Tunisia'),
            'UG' => __('Uganda'),
            'EG' => __('Egypt'),
            'TZ' => __('Tanzania'),
            'BF' => __('Burkina Faso'),
            'ZM' => __('Zambia'),
        ];
    }

    /**
     * Get all the Anctarctican countries.
     */
    public static function antarctica(): array
    {
        return [
            'AQ' => __('Antarctica'),
            'BV' => __('Bouvet Island'),
            'GS' => __('South Georgia and South Sandwich Islands'),
            'TF' => __('French Southern Territories'),
            'HM' => __('Heard and McDonald Islands'),
        ];
    }

    /**
     * Get all the Asian countries.
     */
    public static function asia(): array
    {
        return [
            'AF' => __('Afghanistan'),
            'AZ' => __('Azerbaijan'),
            'BH' => __('Bahrain'),
            'BD' => __('Bangladesh'),
            'AM' => __('Armenia'),
            'BT' => __('Bhutan'),
            'IO' => __('British Indian Ocean Territory'),
            'BN' => __('Brunei'),
            'MM' => __('Myanmar (Burma)'),
            'KH' => __('Cambodia'),
            'LK' => __('Sri Lanka'),
            'CN' => __('China'),
            'TW' => __('Taiwan'),
            'CX' => __('Christmas Island'),
            'CC' => __('Cocos (Keeling) Islands'),
            'GE' => __('Georgia'),
            'PS' => __('Palestinian Territories'),
            'HK' => __('Hong Kong SAR China'),
            'IN' => __('India'),
            'ID' => __('Indonesia'),
            'IR' => __('Iran'),
            'IQ' => __('Iraq'),
            'IL' => __('Israel'),
            'JP' => __('Japan'),
            'KZ' => __('Kazakhstan'),
            'JO' => __('Jordan'),
            'KP' => __('North Korea'),
            'KR' => __('South Korea'),
            'KW' => __('Kuwait'),
            'KG' => __('Kyrgyzstan'),
            'LA' => __('Laos'),
            'LB' => __('Lebanon'),
            'MO' => __('Macao SAR China'),
            'MY' => __('Malaysia'),
            'MV' => __('Maldives'),
            'MN' => __('Mongolia'),
            'OM' => __('Oman'),
            'NP' => __('Nepal'),
            'PK' => __('Pakistan'),
            'PH' => __('Philippines'),
            'TL' => __('Timor-Leste'),
            'QA' => __('Qatar'),
            'RU' => __('Russia'),
            'SA' => __('Saudi Arabia'),
            'SG' => __('Singapore'),
            'VN' => __('Vietnam'),
            'SY' => __('Syria'),
            'TJ' => __('Tajikistan'),
            'TH' => __('Thailand'),
            'AE' => __('United Arab Emirates'),
            'TR' => __('Turkey'),
            'TM' => __('Turkmenistan'),
            'UZ' => __('Uzbekistan'),
            'YE' => __('Yemen'),
        ];
    }

    /**
     * Get all the European countries.
     */
    public static function europe(): array
    {
        return [
            'AL' => __('Albania'),
            'AD' => __('Andorra'),
            'AT' => __('Austria'),
            'BE' => __('Belgium'),
            'BA' => __('Bosnia and Herzegovina'),
            'BG' => __('Bulgaria'),
            'BY' => __('Belarus'),
            'HR' => __('Croatia'),
            'CY' => __('Cyprus'),
            'CZ' => __('Czechia'),
            'DK' => __('Denmark'),
            'EE' => __('Estonia'),
            'FO' => __('Faroe Islands'),
            'FI' => __('Finland'),
            'AX' => __('Åland Islands'),
            'FR' => __('France'),
            'DE' => __('Germany'),
            'GI' => __('Gibraltar'),
            'GR' => __('Greece'),
            'VA' => __('Vatican City'),
            'HU' => __('Hungary'),
            'IS' => __('Iceland'),
            'IE' => __('Ireland'),
            'IT' => __('Italy'),
            'LV' => __('Latvia'),
            'LI' => __('Liechtenstein'),
            'LT' => __('Lithuania'),
            'LU' => __('Luxembourg'),
            'MT' => __('Malta'),
            'MC' => __('Monaco'),
            'MD' => __('Moldova'),
            'ME' => __('Montenegro'),
            'NL' => __('Netherlands'),
            'NO' => __('Norway'),
            'PL' => __('Poland'),
            'PT' => __('Portugal'),
            'RO' => __('Romania'),
            'SM' => __('San Marino'),
            'RS' => __('Serbia'),
            'SK' => __('Slovakia'),
            'SI' => __('Slovenia'),
            'ES' => __('Spain'),
            'SJ' => __('Svalbard and Jan Mayen'),
            'SE' => __('Sweden'),
            'CH' => __('Switzerland'),
            'UA' => __('Ukraine'),
            'MK' => __('North Macedonia'),
            'GB' => __('United Kingdom'),
            'GG' => __('Guernsey'),
            'JE' => __('Jersey'),
            'IM' => __('Isle of Man'),
        ];
    }

    /**
     * Get all the North American countries.
     */
    public static function northAmerica(): array
    {
        return [
            'AG' => __('Antigua and Barbuda'),
            'BS' => __('Bahamas'),
            'BB' => __('Barbados'),
            'BM' => __('Bermuda'),
            'BZ' => __('Belize'),
            'VG' => __('British Virgin Islands'),
            'CA' => __('Canada'),
            'KY' => __('Cayman Islands'),
            'CR' => __('Costa Rica'),
            'CU' => __('Cuba'),
            'DM' => __('Dominica'),
            'DO' => __('Dominican Republic'),
            'SV' => __('El Salvador'),
            'GL' => __('Greenland'),
            'GD' => __('Grenada'),
            'GP' => __('Guadeloupe'),
            'GT' => __('Guatemala'),
            'HT' => __('Haiti'),
            'HN' => __('Honduras'),
            'JM' => __('Jamaica'),
            'MQ' => __('Martinique'),
            'MX' => __('Mexico'),
            'MS' => __('Montserrat'),
            'CW' => __('Curaçao'),
            'AW' => __('Aruba'),
            'SX' => __('Sint Maarten'),
            'BQ' => __('Caribbean Netherlands'),
            'NI' => __('Nicaragua'),
            'PA' => __('Panama'),
            'PR' => __('Puerto Rico'),
            'BL' => __('St. Barthélemy'),
            'KN' => __('St. Kitts and Nevis'),
            'AI' => __('Anguilla'),
            'LC' => __('St. Lucia'),
            'MF' => __('St. Martin'),
            'PM' => __('St. Pierre and Miquelon'),
            'VC' => __('St. Vincent and Grenadines'),
            'TT' => __('Trinidad and Tobago'),
            'TC' => __('Turks and Caicos Islands'),
            'US' => __('United States'),
            'VI' => __('U.S. Virgin Islands'),
        ];
    }

    /**
     * Get all the South American countries.
     */
    public static function southAmerica(): array
    {
        return [
            'AR' => __('Argentina'),
            'BO' => __('Bolivia'),
            'BR' => __('Brazil'),
            'CL' => __('Chile'),
            'CO' => __('Colombia'),
            'EC' => __('Ecuador'),
            'FK' => __('Falkland Islands'),
            'GF' => __('French Guiana'),
            'GY' => __('Guyana'),
            'PY' => __('Paraguay'),
            'PE' => __('Peru'),
            'SR' => __('Suriname'),
            'UY' => __('Uruguay'),
            'VE' => __('Venezuela'),
        ];
    }

    /**
     * Get all the Oceanian countries.
     */
    public static function oceania(): array
    {
        return [
            'AS' => __('American Samoa'),
            'AU' => __('Australia'),
            'SB' => __('Solomon Islands'),
            'CK' => __('Cook Islands'),
            'FJ' => __('Fiji'),
            'PF' => __('French Polynesia'),
            'KI' => __('Kiribati'),
            'GU' => __('Guam'),
            'NR' => __('Nauru'),
            'NC' => __('New Caledonia'),
            'VU' => __('Vanuatu'),
            'NZ' => __('New Zealand'),
            'NU' => __('Niue'),
            'NF' => __('Norfolk Island'),
            'MP' => __('Northern Mariana Islands'),
            'UM' => __('U.S. Outlying Islands'),
            'FM' => __('Micronesia'),
            'MH' => __('Marshall Islands'),
            'PW' => __('Palau'),
            'PG' => __('Papua New Guinea'),
            'PN' => __('Pitcairn Islands'),
            'TK' => __('Tokelau'),
            'TO' => __('Tonga'),
            'TV' => __('Tuvalu'),
            'WF' => __('Wallis and Futuna'),
            'WS' => __('Samoa'),
        ];
    }

    /**
     * Get the name of the given country.
     */
    public static function name(string $country): string
    {
        $countries = array_merge(...static::all());

        return $countries[$country] ?? $country;
    }

    /**
     * Get all the countries grouped by their continent.
     */
    public static function allByContient(): array
    {
        return [
            __('Africa') => static::africa(),
            __('Anctartica') =>  static::antarctica(),
            __('Asia') => static::asia(),
            __('Europe') => static::europe(),
            __('North America') => static::northAmerica(),
            __('South America') => static::southAmerica(),
            __('Oceania') => static::oceania()
        ];
    }

    /**
     * Get all the countries.
     */
    public static function all(): array
    {
        return array_merge(
            static::africa(),
            static::antarctica(),
            static::asia(),
            static::europe(),
            static::northAmerica(),
            static::southAmerica(),
            static::oceania()
        );
    }
}
