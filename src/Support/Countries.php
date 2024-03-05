<?php

namespace Cone\Bazar\Support;

use Illuminate\Support\Facades\Lang;

abstract class Countries
{
    /**
     * Get all the African countries.
     */
    public static function africa(): array
    {
        return [
            'DZ' => Lang::get('bazar::countries.DZ'),
            'AO' => Lang::get('bazar::countries.AO'),
            'BW' => Lang::get('bazar::countries.BW'),
            'BI' => Lang::get('bazar::countries.BI'),
            'CM' => Lang::get('bazar::countries.CM'),
            'CV' => Lang::get('bazar::countries.CV'),
            'CF' => Lang::get('bazar::countries.CF'),
            'TD' => Lang::get('bazar::countries.TD'),
            'KM' => Lang::get('bazar::countries.KM'),
            'YT' => Lang::get('bazar::countries.YT'),
            'CG' => Lang::get('bazar::countries.CG'),
            'CD' => Lang::get('bazar::countries.CD'),
            'BJ' => Lang::get('bazar::countries.BJ'),
            'GQ' => Lang::get('bazar::countries.GQ'),
            'ET' => Lang::get('bazar::countries.ET'),
            'ER' => Lang::get('bazar::countries.ER'),
            'DJ' => Lang::get('bazar::countries.DJ'),
            'GA' => Lang::get('bazar::countries.GA'),
            'GM' => Lang::get('bazar::countries.GM'),
            'GH' => Lang::get('bazar::countries.GH'),
            'GN' => Lang::get('bazar::countries.GN'),
            'CI' => Lang::get('bazar::countries.CI'),
            'KE' => Lang::get('bazar::countries.KE'),
            'LS' => Lang::get('bazar::countries.LS'),
            'LR' => Lang::get('bazar::countries.LR'),
            'LY' => Lang::get('bazar::countries.LY'),
            'MG' => Lang::get('bazar::countries.MG'),
            'MW' => Lang::get('bazar::countries.MW'),
            'ML' => Lang::get('bazar::countries.ML'),
            'MR' => Lang::get('bazar::countries.MR'),
            'MU' => Lang::get('bazar::countries.MU'),
            'MA' => Lang::get('bazar::countries.MA'),
            'MZ' => Lang::get('bazar::countries.MZ'),
            'NA' => Lang::get('bazar::countries.NA'),
            'NE' => Lang::get('bazar::countries.NE'),
            'NG' => Lang::get('bazar::countries.NG'),
            'GW' => Lang::get('bazar::countries.GW'),
            'RE' => Lang::get('bazar::countries.RE'),
            'RW' => Lang::get('bazar::countries.RW'),
            'SH' => Lang::get('bazar::countries.SH'),
            'ST' => Lang::get('bazar::countries.ST'),
            'SN' => Lang::get('bazar::countries.SN'),
            'SC' => Lang::get('bazar::countries.SC'),
            'SL' => Lang::get('bazar::countries.SL'),
            'SO' => Lang::get('bazar::countries.SO'),
            'ZA' => Lang::get('bazar::countries.ZA'),
            'ZW' => Lang::get('bazar::countries.ZW'),
            'SS' => Lang::get('bazar::countries.SS'),
            'EH' => Lang::get('bazar::countries.EH'),
            'SD' => Lang::get('bazar::countries.SD'),
            'SZ' => Lang::get('bazar::countries.SZ'),
            'TG' => Lang::get('bazar::countries.TG'),
            'TN' => Lang::get('bazar::countries.TN'),
            'UG' => Lang::get('bazar::countries.UG'),
            'EG' => Lang::get('bazar::countries.EG'),
            'TZ' => Lang::get('bazar::countries.TZ'),
            'BF' => Lang::get('bazar::countries.BF'),
            'ZM' => Lang::get('bazar::countries.ZM'),
        ];
    }

    /**
     * Get all the Anctarctican countries.
     */
    public static function antarctica(): array
    {
        return [
            'AQ' => Lang::get('bazar::countries.AQ'),
            'BV' => Lang::get('bazar::countries.BV'),
            'GS' => Lang::get('bazar::countries.GS'),
            'TF' => Lang::get('bazar::countries.TF'),
            'HM' => Lang::get('bazar::countries.HM'),
        ];
    }

    /**
     * Get all the Asian countries.
     */
    public static function asia(): array
    {
        return [
            'AF' => Lang::get('bazar::countries.AF'),
            'AZ' => Lang::get('bazar::countries.AZ'),
            'BH' => Lang::get('bazar::countries.BH'),
            'BD' => Lang::get('bazar::countries.BD'),
            'AM' => Lang::get('bazar::countries.AM'),
            'BT' => Lang::get('bazar::countries.BT'),
            'IO' => Lang::get('bazar::countries.IO'),
            'BN' => Lang::get('bazar::countries.BN'),
            'MM' => Lang::get('bazar::countries.MM'),
            'KH' => Lang::get('bazar::countries.KH'),
            'LK' => Lang::get('bazar::countries.LK'),
            'CN' => Lang::get('bazar::countries.CN'),
            'TW' => Lang::get('bazar::countries.TW'),
            'CX' => Lang::get('bazar::countries.CX'),
            'CC' => Lang::get('bazar::countries.CC'),
            'GE' => Lang::get('bazar::countries.GE'),
            'PS' => Lang::get('bazar::countries.PS'),
            'HK' => Lang::get('bazar::countries.HK'),
            'IN' => Lang::get('bazar::countries.IN'),
            'ID' => Lang::get('bazar::countries.ID'),
            'IR' => Lang::get('bazar::countries.IR'),
            'IQ' => Lang::get('bazar::countries.IQ'),
            'IL' => Lang::get('bazar::countries.IL'),
            'JP' => Lang::get('bazar::countries.JP'),
            'KZ' => Lang::get('bazar::countries.KZ'),
            'JO' => Lang::get('bazar::countries.JO'),
            'KP' => Lang::get('bazar::countries.KP'),
            'KR' => Lang::get('bazar::countries.KR'),
            'KW' => Lang::get('bazar::countries.KW'),
            'KG' => Lang::get('bazar::countries.KG'),
            'LA' => Lang::get('bazar::countries.LA'),
            'LB' => Lang::get('bazar::countries.LB'),
            'MO' => Lang::get('bazar::countries.MO'),
            'MY' => Lang::get('bazar::countries.MY'),
            'MV' => Lang::get('bazar::countries.MV'),
            'MN' => Lang::get('bazar::countries.MN'),
            'OM' => Lang::get('bazar::countries.OM'),
            'NP' => Lang::get('bazar::countries.NP'),
            'PK' => Lang::get('bazar::countries.PK'),
            'PH' => Lang::get('bazar::countries.PH'),
            'TL' => Lang::get('bazar::countries.TL'),
            'QA' => Lang::get('bazar::countries.QA'),
            'RU' => Lang::get('bazar::countries.RU'),
            'SA' => Lang::get('bazar::countries.SA'),
            'SG' => Lang::get('bazar::countries.SG'),
            'VN' => Lang::get('bazar::countries.VN'),
            'SY' => Lang::get('bazar::countries.SY'),
            'TJ' => Lang::get('bazar::countries.TJ'),
            'TH' => Lang::get('bazar::countries.TH'),
            'AE' => Lang::get('bazar::countries.AE'),
            'TR' => Lang::get('bazar::countries.TR'),
            'TM' => Lang::get('bazar::countries.TM'),
            'UZ' => Lang::get('bazar::countries.UZ'),
            'YE' => Lang::get('bazar::countries.YE'),
        ];
    }

    /**
     * Get all the European countries.
     */
    public static function europe(): array
    {
        return [
            'AL' => Lang::get('bazar::countries.AL'),
            'AD' => Lang::get('bazar::countries.AD'),
            'AT' => Lang::get('bazar::countries.AT'),
            'BE' => Lang::get('bazar::countries.BE'),
            'BA' => Lang::get('bazar::countries.BA'),
            'BG' => Lang::get('bazar::countries.BG'),
            'BY' => Lang::get('bazar::countries.BY'),
            'HR' => Lang::get('bazar::countries.HR'),
            'CY' => Lang::get('bazar::countries.CY'),
            'CZ' => Lang::get('bazar::countries.CZ'),
            'DK' => Lang::get('bazar::countries.DK'),
            'EE' => Lang::get('bazar::countries.EE'),
            'FO' => Lang::get('bazar::countries.FO'),
            'FI' => Lang::get('bazar::countries.FI'),
            'AX' => Lang::get('bazar::countries.AX'),
            'FR' => Lang::get('bazar::countries.FR'),
            'DE' => Lang::get('bazar::countries.DE'),
            'GI' => Lang::get('bazar::countries.GI'),
            'GR' => Lang::get('bazar::countries.GR'),
            'VA' => Lang::get('bazar::countries.VA'),
            'HU' => Lang::get('bazar::countries.HU'),
            'IS' => Lang::get('bazar::countries.IS'),
            'IE' => Lang::get('bazar::countries.IE'),
            'IT' => Lang::get('bazar::countries.IT'),
            'LV' => Lang::get('bazar::countries.LV'),
            'LI' => Lang::get('bazar::countries.LI'),
            'LT' => Lang::get('bazar::countries.LT'),
            'LU' => Lang::get('bazar::countries.LU'),
            'MT' => Lang::get('bazar::countries.MT'),
            'MC' => Lang::get('bazar::countries.MC'),
            'MD' => Lang::get('bazar::countries.MD'),
            'ME' => Lang::get('bazar::countries.ME'),
            'NL' => Lang::get('bazar::countries.NL'),
            'NO' => Lang::get('bazar::countries.NO'),
            'PL' => Lang::get('bazar::countries.PL'),
            'PT' => Lang::get('bazar::countries.PT'),
            'RO' => Lang::get('bazar::countries.RO'),
            'SM' => Lang::get('bazar::countries.SM'),
            'RS' => Lang::get('bazar::countries.RS'),
            'SK' => Lang::get('bazar::countries.SK'),
            'SI' => Lang::get('bazar::countries.SI'),
            'ES' => Lang::get('bazar::countries.ES'),
            'SJ' => Lang::get('bazar::countries.SJ'),
            'SE' => Lang::get('bazar::countries.SE'),
            'CH' => Lang::get('bazar::countries.CH'),
            'UA' => Lang::get('bazar::countries.UA'),
            'MK' => Lang::get('bazar::countries.MK'),
            'GB' => Lang::get('bazar::countries.GB'),
            'GG' => Lang::get('bazar::countries.GG'),
            'JE' => Lang::get('bazar::countries.JE'),
            'IM' => Lang::get('bazar::countries.IM'),
        ];
    }

    /**
     * Get all the North American countries.
     */
    public static function northAmerica(): array
    {
        return [
            'AG' => Lang::get('bazar::countries.AG'),
            'BS' => Lang::get('bazar::countries.BS'),
            'BB' => Lang::get('bazar::countries.BB'),
            'BM' => Lang::get('bazar::countries.BM'),
            'BZ' => Lang::get('bazar::countries.BZ'),
            'VG' => Lang::get('bazar::countries.VG'),
            'CA' => Lang::get('bazar::countries.CA'),
            'KY' => Lang::get('bazar::countries.KY'),
            'CR' => Lang::get('bazar::countries.CR'),
            'CU' => Lang::get('bazar::countries.CU'),
            'DM' => Lang::get('bazar::countries.DM'),
            'DO' => Lang::get('bazar::countries.DO'),
            'SV' => Lang::get('bazar::countries.SV'),
            'GL' => Lang::get('bazar::countries.GL'),
            'GD' => Lang::get('bazar::countries.GD'),
            'GP' => Lang::get('bazar::countries.GP'),
            'GT' => Lang::get('bazar::countries.GT'),
            'HT' => Lang::get('bazar::countries.HT'),
            'HN' => Lang::get('bazar::countries.HN'),
            'JM' => Lang::get('bazar::countries.JM'),
            'MQ' => Lang::get('bazar::countries.MQ'),
            'MX' => Lang::get('bazar::countries.MX'),
            'MS' => Lang::get('bazar::countries.MS'),
            'CW' => Lang::get('bazar::countries.CW'),
            'AW' => Lang::get('bazar::countries.AW'),
            'SX' => Lang::get('bazar::countries.SX'),
            'BQ' => Lang::get('bazar::countries.BQ'),
            'NI' => Lang::get('bazar::countries.NI'),
            'PA' => Lang::get('bazar::countries.PA'),
            'PR' => Lang::get('bazar::countries.PR'),
            'BL' => Lang::get('bazar::countries.BL'),
            'KN' => Lang::get('bazar::countries.KN'),
            'AI' => Lang::get('bazar::countries.AI'),
            'LC' => Lang::get('bazar::countries.LC'),
            'MF' => Lang::get('bazar::countries.MF'),
            'PM' => Lang::get('bazar::countries.PM'),
            'VC' => Lang::get('bazar::countries.VC'),
            'TT' => Lang::get('bazar::countries.TT'),
            'TC' => Lang::get('bazar::countries.TC'),
            'US' => Lang::get('bazar::countries.US'),
            'VI' => Lang::get('bazar::countries.VI'),
        ];
    }

    /**
     * Get all the South American countries.
     */
    public static function southAmerica(): array
    {
        return [
            'AR' => Lang::get('bazar::countries.AR'),
            'BO' => Lang::get('bazar::countries.BO'),
            'BR' => Lang::get('bazar::countries.BR'),
            'CL' => Lang::get('bazar::countries.CL'),
            'CO' => Lang::get('bazar::countries.CO'),
            'EC' => Lang::get('bazar::countries.EC'),
            'FK' => Lang::get('bazar::countries.FK'),
            'GF' => Lang::get('bazar::countries.GF'),
            'GY' => Lang::get('bazar::countries.GY'),
            'PY' => Lang::get('bazar::countries.PY'),
            'PE' => Lang::get('bazar::countries.PE'),
            'SR' => Lang::get('bazar::countries.SR'),
            'UY' => Lang::get('bazar::countries.UY'),
            'VE' => Lang::get('bazar::countries.VE'),
        ];
    }

    /**
     * Get all the Oceanian countries.
     */
    public static function oceania(): array
    {
        return [
            'AS' => Lang::get('bazar::countries.AS'),
            'AU' => Lang::get('bazar::countries.AU'),
            'SB' => Lang::get('bazar::countries.SB'),
            'CK' => Lang::get('bazar::countries.CK'),
            'FJ' => Lang::get('bazar::countries.FJ'),
            'PF' => Lang::get('bazar::countries.PF'),
            'KI' => Lang::get('bazar::countries.KI'),
            'GU' => Lang::get('bazar::countries.GU'),
            'NR' => Lang::get('bazar::countries.NR'),
            'NC' => Lang::get('bazar::countries.NC'),
            'VU' => Lang::get('bazar::countries.VU'),
            'NZ' => Lang::get('bazar::countries.NZ'),
            'NU' => Lang::get('bazar::countries.NU'),
            'NF' => Lang::get('bazar::countries.NF'),
            'MP' => Lang::get('bazar::countries.MP'),
            'UM' => Lang::get('bazar::countries.UM'),
            'FM' => Lang::get('bazar::countries.FM'),
            'MH' => Lang::get('bazar::countries.MH'),
            'PW' => Lang::get('bazar::countries.PW'),
            'PG' => Lang::get('bazar::countries.PG'),
            'PN' => Lang::get('bazar::countries.PN'),
            'TK' => Lang::get('bazar::countries.TK'),
            'TO' => Lang::get('bazar::countries.TO'),
            'TV' => Lang::get('bazar::countries.TV'),
            'WF' => Lang::get('bazar::countries.WF'),
            'WS' => Lang::get('bazar::countries.WS'),
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
            __('Anctartica') => static::antarctica(),
            __('Asia') => static::asia(),
            __('Europe') => static::europe(),
            __('North America') => static::northAmerica(),
            __('South America') => static::southAmerica(),
            __('Oceania') => static::oceania(),
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
