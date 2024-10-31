<?php

function ops_get_home_url()
{
    $home_url = get_home_url();

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        // check if home has https
        if (stristr($home_url, 'https://') === false) {
            $home_url = str_replace('http://', 'https://', $home_url);
        }
    }

    return $home_url;
}

function ops_remove_post_term($post_id, $term, $taxonomy)
{

    if (!is_numeric($term)) {
        $term = get_term_by('name', $term, $taxonomy);
        if (!$term || is_wp_error($term)) return false;
        $term_id = $term->term_id;
    } else {
        $term_id = $term;
    }

    // Get the existing terms and only keep the ones we don't want removed
    $new_terms = array();
    $current_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'ids'));

    foreach ($current_terms as $current_term) {
        if ($current_term != $term_id) $new_terms[] = intval($current_term);
    }

    return wp_set_object_terms($post_id, $new_terms, $taxonomy);
}

function ops_is_premium()
{
    return false;
}

function ops_the_reciprocal_status($pid)
{
    global $ops;
    $settings = $ops->get_settings();

    $reciprocal_check = get_post_meta($pid, 'reciprocal_check', true);
    $reciprocal_status = get_post_meta($pid, 'reciprocal_check_status', true);
    $reciprocal_last = get_post_meta($pid, 'reciprocal_check_last', true);

    $status_map = [
        'ok' => 'OK',
        'not_found' => 'Not found',
        'nofollow' => 'OK, nofollow',
        'not_checked' => 'Not checked'
    ];
    ?>
    <?php if (empty($reciprocal_status)): ?>
    -
<?php else: ?>
    <div class="ops-reciprocal-status ops-status-<?php echo $reciprocal_status ?>">
        <?php if (!empty($reciprocal_last)): ?>
            <div class="ops-last-checked">
                Last checked: <br>
                <?php echo date($settings['core_date_format'] . ' H:i:s', $reciprocal_last) ?>
            </div>
        <?php endif; ?>
        <?php echo $status_map[$reciprocal_status] ? $status_map[$reciprocal_status] : $reciprocal_status ?>
    </div>

<?php endif; ?>
    <?php
}

function ops_get_keyword_backlinks($pid)
{

    global $wpdb;
    $query = 'SELECT ID FROM ' . $wpdb->prefix . 'posts INNER JOIN ' . $wpdb->prefix . 'postmeta ON ' . $wpdb->prefix . 'posts.ID = ' . $wpdb->prefix . 'postmeta.post_id WHERE meta_key = \'keyword_id\' AND meta_value = \'' . $pid . '\'';
    $results = $wpdb->get_results($query);

    if (!empty($results)) {
        $ids = [];
        foreach ($results as $result) {
            $ids[] = $result->ID;
        }

        return $ids;
    }

    return false;
}


function ops_get_ranks($pid)
{
    global $wpdb;
    $q = 'SELECT * FROM ' . $wpdb->prefix . 'ops_rankings WHERE keyword_id = "' . $pid . '" ORDER BY time DESC LIMIT 0, 15';
    $db_results = $wpdb->get_results($q, ARRAY_A);

    if (!empty($db_results)) {
        return $db_results;
    } else {
        return false;
    }

}

function ops_get_ranks_difference($old, $new)
{
    if ((int)$old > (int)$new) {
        ?>
        (<span class="ops-c-green">+<?php echo $old - $new ?></span>)
        <?php
    }

    if ((int)$new > (int)$old) {
        ?>
        (<span class="ops-c-red">-<?php echo $new - $old ?></span>)
        <?php
    }
}

function ops_get_graph_colour($i)
{
    $map = [
        0 => '#56D0D6',
        1 => '#00a0d2',
        2 => '#F4953D',
        3 => '#DB5461',
        4 => '#8B86C4',
        5 => '#618C03',
        6 => '#60888A',
        7 => '#9BB7BD',
        8 => '#37868A',
        9 => '#FFD357',
        10 => '#35C795',
        11 => '#F59EBD',
    ];

    return isset($map[$i]) ? $map[$i] : '#00a0d2';
}

function ops_get_lang_array()
{
    $languages = array(
        'aa' => 'Afar',
        'ab' => 'Abkhaz',
        'ae' => 'Avestan',
        'af' => 'Afrikaans',
        'ak' => 'Akan',
        'am' => 'Amharic',
        'an' => 'Aragonese',
        'ar' => 'Arabic',
        'as' => 'Assamese',
        'av' => 'Avaric',
        'ay' => 'Aymara',
        'az' => 'Azerbaijani',
        'ba' => 'Bashkir',
        'be' => 'Belarusian',
        'bg' => 'Bulgarian',
        'bh' => 'Bihari',
        'bi' => 'Bislama',
        'bm' => 'Bambara',
        'bn' => 'Bengali',
        'bo' => 'Tibetan Standard, Tibetan, Central',
        'br' => 'Breton',
        'bs' => 'Bosnian',
        'ca' => 'Catalan; Valencian',
        'ce' => 'Chechen',
        'ch' => 'Chamorro',
        'co' => 'Corsican',
        'cr' => 'Cree',
        'cs' => 'Czech',
        'cv' => 'Chuvash',
        'cy' => 'Welsh',
        'da' => 'Danish',
        'de' => 'German',
        'dv' => 'Divehi; Dhivehi; Maldivian;',
        'dz' => 'Dzongkha',
        'ee' => 'Ewe',
        'el' => 'Greek, Modern',
        'en' => 'English',
        'es' => 'Spanish; Castilian',
        'et' => 'Estonian',
        'eu' => 'Basque',
        'fa' => 'Persian',
        'ff' => 'Fula; Fulah; Pulaar; Pular',
        'fi' => 'Finnish',
        'fj' => 'Fijian',
        'fo' => 'Faroese',
        'fr' => 'French',
        'fy' => 'Western Frisian',
        'ga' => 'Irish',
        'gd' => 'Scottish Gaelic; Gaelic',
        'gl' => 'Galician',
        'gu' => 'Gujarati',
        'gv' => 'Manx',
        'ha' => 'Hausa',
        'he' => 'Hebrew (modern)',
        'hi' => 'Hindi',
        'ho' => 'Hiri Motu',
        'hr' => 'Croatian',
        'ht' => 'Haitian; Haitian Creole',
        'hu' => 'Hungarian',
        'hy' => 'Armenian',
        'hz' => 'Herero',
        'ia' => 'Interlingua',
        'id' => 'Indonesian',
        'ie' => 'Interlingue',
        'ig' => 'Igbo',
        'ii' => 'Nuosu',
        'ik' => 'Inupiaq',
        'io' => 'Ido',
        'is' => 'Icelandic',
        'it' => 'Italian',
        'iu' => 'Inuktitut',
        'ja' => 'Japanese (ja)',
        'jv' => 'Javanese (jv)',
        'ka' => 'Georgian',
        'kg' => 'Kongo',
        'ki' => 'Kikuyu, Gikuyu',
        'kj' => 'Kwanyama, Kuanyama',
        'kk' => 'Kazakh',
        'kl' => 'Kalaallisut, Greenlandic',
        'km' => 'Khmer',
        'kn' => 'Kannada',
        'ko' => 'Korean',
        'kr' => 'Kanuri',
        'ks' => 'Kashmiri',
        'ku' => 'Kurdish',
        'kv' => 'Komi',
        'kw' => 'Cornish',
        'ky' => 'Kirghiz, Kyrgyz',
        'la' => 'Latin',
        'lb' => 'Luxembourgish, Letzeburgesch',
        'lg' => 'Luganda',
        'li' => 'Limburgish, Limburgan, Limburger',
        'ln' => 'Lingala',
        'lo' => 'Lao',
        'lt' => 'Lithuanian',
        'lu' => 'Luba-Katanga',
        'lv' => 'Latvian',
        'mg' => 'Malagasy',
        'mh' => 'Marshallese',
        'mi' => 'Maori',
        'mk' => 'Macedonian',
        'ml' => 'Malayalam',
        'mn' => 'Mongolian',
        'mr' => 'Marathi (Mara?hi)',
        'ms' => 'Malay',
        'mt' => 'Maltese',
        'my' => 'Burmese',
        'na' => 'Nauru',
        'nb' => 'Norwegian BokmÃ¥l',
        'nd' => 'North Ndebele',
        'ne' => 'Nepali',
        'ng' => 'Ndonga',
        'nl' => 'Dutch',
        'nn' => 'Norwegian Nynorsk',
        'no' => 'Norwegian',
        'nr' => 'South Ndebele',
        'nv' => 'Navajo, Navaho',
        'ny' => 'Chichewa; Chewa; Nyanja',
        'oc' => 'Occitan',
        'oj' => 'Ojibwe, Ojibwa',
        'om' => 'Oromo',
        'or' => 'Oriya',
        'os' => 'Ossetian, Ossetic',
        'pa' => 'Panjabi, Punjabi',
        'pi' => 'Pali',
        'pl' => 'Polish',
        'ps' => 'Pashto, Pushto',
        'pt' => 'Portuguese',
        'qu' => 'Quechua',
        'rm' => 'Romansh',
        'rn' => 'Kirundi',
        'ro' => 'Romanian, Moldavian, Moldovan',
        'ru' => 'Russian',
        'rw' => 'Kinyarwanda',
        'sa' => 'Sanskrit',
        'sc' => 'Sardinian',
        'sd' => 'Sindhi',
        'se' => 'Northern Sami',
        'sg' => 'Sango',
        'si' => 'Sinhala, Sinhalese',
        'sk' => 'Slovak',
        'sl' => 'Slovene',
        'sm' => 'Samoan',
        'sn' => 'Shona',
        'so' => 'Somali',
        'sq' => 'Albanian',
        'sr' => 'Serbian',
        'ss' => 'Swati',
        'st' => 'Southern Sotho',
        'su' => 'Sundanese',
        'sv' => 'Swedish',
        'sw' => 'Swahili',
        'ta' => 'Tamil',
        'te' => 'Telugu',
        'tg' => 'Tajik',
        'th' => 'Thai',
        'ti' => 'Tigrinya',
        'tk' => 'Turkmen',
        'tl' => 'Tagalog',
        'tn' => 'Tswana',
        'to' => 'Tonga (Tonga Islands)',
        'tr' => 'Turkish',
        'ts' => 'Tsonga',
        'tt' => 'Tatar',
        'tw' => 'Twi',
        'ty' => 'Tahitian',
        'ug' => 'Uighur, Uyghur',
        'uk' => 'Ukrainian',
        'ur' => 'Urdu',
        'uz' => 'Uzbek',
        've' => 'Venda',
        'vi' => 'Vietnamese',
        'wa' => 'Walloon',
        'wo' => 'Wolof',
        'xh' => 'Xhosa',
        'yi' => 'Yiddish',
        'yo' => 'Yoruba',
        'za' => 'Zhuang, Chuang',
        'zh' => 'Chinese',
        'zu' => 'Zulu',
    );
    return $languages;
}

function ops_get_countries_array()
{
    $country_array = array(
        "AF" => "Afghanistan",
        "AL" => "Albania",
        "DZ" => "Algeria",
        "AS" => "American Samoa",
        "AD" => "Andorra",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AQ" => "Antarctica",
        "AG" => "Antigua and Barbuda",
        "AR" => "Argentina",
        "AM" => "Armenia",
        "AW" => "Aruba",
        "AU" => "Australia",
        "AT" => "Austria",
        "AZ" => "Azerbaijan",
        "BS" => "Bahamas",
        "BH" => "Bahrain",
        "BD" => "Bangladesh",
        "BB" => "Barbados",
        "BY" => "Belarus",
        "BE" => "Belgium",
        "BZ" => "Belize",
        "BJ" => "Benin",
        "BM" => "Bermuda",
        "BT" => "Bhutan",
        "BO" => "Bolivia",
        "BA" => "Bosnia and Herzegovina",
        "BW" => "Botswana",
        "BV" => "Bouvet Island",
        "BR" => "Brazil",
        "BQ" => "British Antarctic Territory",
        "IO" => "British Indian Ocean Territory",
        "VG" => "British Virgin Islands",
        "BN" => "Brunei",
        "BG" => "Bulgaria",
        "BF" => "Burkina Faso",
        "BI" => "Burundi",
        "KH" => "Cambodia",
        "CM" => "Cameroon",
        "CA" => "Canada",
        "CT" => "Canton and Enderbury Islands",
        "CV" => "Cape Verde",
        "KY" => "Cayman Islands",
        "CF" => "Central African Republic",
        "TD" => "Chad",
        "CL" => "Chile",
        "CN" => "China",
        "CX" => "Christmas Island",
        "CC" => "Cocos [Keeling] Islands",
        "CO" => "Colombia",
        "KM" => "Comoros",
        "CG" => "Congo - Brazzaville",
        "CD" => "Congo - Kinshasa",
        "CK" => "Cook Islands",
        "CR" => "Costa Rica",
        "HR" => "Croatia",
        "CU" => "Cuba",
        "CY" => "Cyprus",
        "CZ" => "Czech Republic",
        "CI" => "Côte d’Ivoire",
        "DK" => "Denmark",
        "DJ" => "Djibouti",
        "DM" => "Dominica",
        "DO" => "Dominican Republic",
        "NQ" => "Dronning Maud Land",
        "DD" => "East Germany",
        "EC" => "Ecuador",
        "EG" => "Egypt",
        "SV" => "El Salvador",
        "GQ" => "Equatorial Guinea",
        "ER" => "Eritrea",
        "EE" => "Estonia",
        "ET" => "Ethiopia",
        "FK" => "Falkland Islands",
        "FO" => "Faroe Islands",
        "FJ" => "Fiji",
        "FI" => "Finland",
        "FR" => "France",
        "GF" => "French Guiana",
        "PF" => "French Polynesia",
        "TF" => "French Southern Territories",
        "FQ" => "French Southern and Antarctic Territories",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GE" => "Georgia",
        "DE" => "Germany",
        "GH" => "Ghana",
        "GI" => "Gibraltar",
        "GR" => "Greece",
        "GL" => "Greenland",
        "GD" => "Grenada",
        "GP" => "Guadeloupe",
        "GU" => "Guam",
        "GT" => "Guatemala",
        "GG" => "Guernsey",
        "GN" => "Guinea",
        "GW" => "Guinea-Bissau",
        "GY" => "Guyana",
        "HT" => "Haiti",
        "HM" => "Heard Island and McDonald Islands",
        "HN" => "Honduras",
        "HK" => "Hong Kong SAR China",
        "HU" => "Hungary",
        "IS" => "Iceland",
        "IN" => "India",
        "ID" => "Indonesia",
        "IR" => "Iran",
        "IQ" => "Iraq",
        "IE" => "Ireland",
        "IM" => "Isle of Man",
        "IL" => "Israel",
        "IT" => "Italy",
        "JM" => "Jamaica",
        "JP" => "Japan",
        "JE" => "Jersey",
        "JT" => "Johnston Island",
        "JO" => "Jordan",
        "KZ" => "Kazakhstan",
        "KE" => "Kenya",
        "KI" => "Kiribati",
        "KW" => "Kuwait",
        "KG" => "Kyrgyzstan",
        "LA" => "Laos",
        "LV" => "Latvia",
        "LB" => "Lebanon",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "Libya",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "MO" => "Macau SAR China",
        "MK" => "Macedonia",
        "MG" => "Madagascar",
        "MW" => "Malawi",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malta",
        "MH" => "Marshall Islands",
        "MQ" => "Martinique",
        "MR" => "Mauritania",
        "MU" => "Mauritius",
        "YT" => "Mayotte",
        "FX" => "Metropolitan France",
        "MX" => "Mexico",
        "FM" => "Micronesia",
        "MI" => "Midway Islands",
        "MD" => "Moldova",
        "MC" => "Monaco",
        "MN" => "Mongolia",
        "ME" => "Montenegro",
        "MS" => "Montserrat",
        "MA" => "Morocco",
        "MZ" => "Mozambique",
        "MM" => "Myanmar [Burma]",
        "NA" => "Namibia",
        "NR" => "Nauru",
        "NP" => "Nepal",
        "NL" => "Netherlands",
        "AN" => "Netherlands Antilles",
        "NT" => "Neutral Zone",
        "NC" => "New Caledonia",
        "NZ" => "New Zealand",
        "NI" => "Nicaragua",
        "NE" => "Niger",
        "NG" => "Nigeria",
        "NU" => "Niue",
        "NF" => "Norfolk Island",
        "KP" => "North Korea",
        "VD" => "North Vietnam",
        "MP" => "Northern Mariana Islands",
        "NO" => "Norway",
        "OM" => "Oman",
        "PC" => "Pacific Islands Trust Territory",
        "PK" => "Pakistan",
        "PW" => "Palau",
        "PS" => "Palestinian Territories",
        "PA" => "Panama",
        "PZ" => "Panama Canal Zone",
        "PG" => "Papua New Guinea",
        "PY" => "Paraguay",
        "YD" => "People's Democratic Republic of Yemen",
        "PE" => "Peru",
        "PH" => "Philippines",
        "PN" => "Pitcairn Islands",
        "PL" => "Poland",
        "PT" => "Portugal",
        "PR" => "Puerto Rico",
        "QA" => "Qatar",
        "RO" => "Romania",
        "RU" => "Russia",
        "RW" => "Rwanda",
        "RE" => "Réunion",
        "BL" => "Saint Barthélemy",
        "SH" => "Saint Helena",
        "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia",
        "MF" => "Saint Martin",
        "PM" => "Saint Pierre and Miquelon",
        "VC" => "Saint Vincent and the Grenadines",
        "WS" => "Samoa",
        "SM" => "San Marino",
        "SA" => "Saudi Arabia",
        "SN" => "Senegal",
        "RS" => "Serbia",
        "CS" => "Serbia and Montenegro",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SG" => "Singapore",
        "SK" => "Slovakia",
        "SI" => "Slovenia",
        "SB" => "Solomon Islands",
        "SO" => "Somalia",
        "ZA" => "South Africa",
        "GS" => "South Georgia and the South Sandwich Islands",
        "KR" => "South Korea",
        "ES" => "Spain",
        "LK" => "Sri Lanka",
        "SD" => "Sudan",
        "SR" => "Suriname",
        "SJ" => "Svalbard and Jan Mayen",
        "SZ" => "Swaziland",
        "SE" => "Sweden",
        "CH" => "Switzerland",
        "SY" => "Syria",
        "ST" => "São Tomé and Príncipe",
        "TW" => "Taiwan",
        "TJ" => "Tajikistan",
        "TZ" => "Tanzania",
        "TH" => "Thailand",
        "TL" => "Timor-Leste",
        "TG" => "Togo",
        "TK" => "Tokelau",
        "TO" => "Tonga",
        "TT" => "Trinidad and Tobago",
        "TN" => "Tunisia",
        "TR" => "Turkey",
        "TM" => "Turkmenistan",
        "TC" => "Turks and Caicos Islands",
        "TV" => "Tuvalu",
        "UM" => "U.S. Minor Outlying Islands",
        "PU" => "U.S. Miscellaneous Pacific Islands",
        "VI" => "U.S. Virgin Islands",
        "UG" => "Uganda",
        "UA" => "Ukraine",
        "SU" => "Union of Soviet Socialist Republics",
        "AE" => "United Arab Emirates",
        "GB" => "United Kingdom",
        "US" => "United States",
        "ZZ" => "Unknown or Invalid Region",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan",
        "VU" => "Vanuatu",
        "VA" => "Vatican City",
        "VE" => "Venezuela",
        "VN" => "Vietnam",
        "WK" => "Wake Island",
        "WF" => "Wallis and Futuna",
        "EH" => "Western Sahara",
        "YE" => "Yemen",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe",
        "AX" => "Åland Islands",
    );

    return $country_array;
}

function ops_get_google_domains_array()
{
    $google_domains = array(
        "com" => "Default - google.com",
        "as" => "American Samoa - google.as",
        "off.ai" => "Anguilla - google.off.ai",
        "com.ag" => "Antigua and Barbuda - google.com.ag",
        "com.ar" => "Argentina - google.com.ar",
        "com.au" => "Australia - google.com.au",
        "at" => "Austria - google.at",
        "az" => "Azerbaijan - google.az",
        "be" => "Belgium - google.be",
        "com.br" => "Brazil - google.com.br",
        "vg" => "British Virgin Islands - google.vg",
        "bi" => "Burundi - google.bi",
        "ca" => "Canada - google.ca",
        "td" => "Chad - google.td",
        "cl" => "Chile - google.cl",
        "com.co" => "Colombia - google.com.co",
        "co.cr" => "Costa Rica - google.co.cr",
        "ci" => "Côte dIvoire - google.ci",
        "com.cu" => "Cuba - google.com.cu",
        "cz" => "Czech Republic - google.cz",
        "cd" => "Dem. Rep. of the Congo - google.cd",
        "dk" => "Denmark - google.dk",
        "dj" => "Djibouti - google.dj",
        "com.do" => "Dominican Republic - google.com.do",
        "com.ec" => "Ecuador - google.com.ec",
        "com.sv" => "El Salvador - google.com.sv",
        "fm" => "Federated States of Micronesia - google.fm",
        "com.fj" => "Fiji - google.com.fj",
        "fi" => "Finland - google.fi",
        "fr" => "France - google.fr",
        "gm" => "The Gambia - google.gm",
        "ge" => "Georgia - google.ge",
        "de" => "Germany - google.de",
        "com.gi" => "Gibraltar - google.com.gi",
        "com.gr" => "Greece - google.com.gr",
        "gl" => "Greenland - google.gl",
        "gg" => "Guernsey - google.gg",
        "hn" => "Honduras - google.hn",
        "com.hk" => "Hong Kong - google.com.hk",
        "co.hu" => "Hungary - google.co.hu",
        "co.in" => "India - google.co.in",
        "ie" => "Ireland - google.ie",
        "co.im" => "Isle of Man - google.co.im",
        "co.il" => "Israel - google.co.il",
        "it" => "Italy - google.it",
        "com.jm" => "Jamaica - google.com.jm",
        "co.jp" => "Japan - google.co.jp",
        "co.je" => "Jersey - google.co.je",
        "kz" => "Kazakhstan - google.kz",
        "co.kr" => "Korea - google.co.kr",
        "lv" => "Latvia - google.lv",
        "co.ls" => "Lesotho - google.co.ls",
        "li" => "Liechtenstein - google.li",
        "lt" => "Lithuania - google.lt",
        "lu" => "Luxembourg - google.lu",
        "mw" => "Malawi - google.mw",
        "com.my" => "Malaysia - google.com.my",
        "com.mt" => "Malta - google.com.mt",
        "mu" => "Mauritius - google.mu",
        "com.mx" => "México - google.com.mx",
        "ms" => "Montserrat - google.ms",
        "com.na" => "Namibia - google.com.na",
        "com.np" => "Nepal - google.com.np",
        "nl" => "Netherlands - google.nl",
        "co.nz" => "New Zealand - google.co.nz",
        "com.ni" => "Nicaragua - google.com.ni",
        "com.nf" => "Norfolk Island - google.com.nf",
        "com.pk" => "Pakistan - google.com.pk",
        "com.pa" => "Panamá - google.com.pa",
        "com.py" => "Paraguay - google.com.py",
        "com.pe" => "Perú - google.com.pe",
        "com.ph" => "Philippines - google.com.ph",
        "pn" => "Pitcairn Islands - google.pn",
        "pl" => "Poland - google.pl",
        "pt" => "Portugal - google.pt",
        "com.pr" => "Puerto Rico - google.com.pr",
        "cg" => "Rep. of the Congo - google.cg",
        "ro" => "Romania - google.ro",
        "ru" => "Russia - google.ru",
        "rw" => "Rwanda - google.rw",
        "sh" => "Saint Helena - google.sh",
        "sm" => "San Marino - google.sm",
        "com.sg" => "Singapore - google.com.sg",
        "sk" => "Slovakia - google.sk",
        "co.za" => "South Africa - google.co.za",
        "es" => "Spain - google.es",
        "se" => "Sweden - google.se",
        "ch" => "Switzerland - google.ch",
        "com.tw" => "Taiwan - google.com.tw",
        "co.th" => "Thailand - google.co.th",
        "tt" => "Trinidad and Tobago - google.tt",
        "com.tr" => "Turkey - google.com.tr",
        "com.ua" => "Ukraine - google.com.ua",
        "ae" => "United Arab Emirates - google.ae",
        "co.uk" => "United Kingdom - google.co.uk",
        "com.uy" => "Uruguay - google.com.uy",
        "uz" => "Uzbekistan - google.uz",
        "vu" => "Vanuatu - google.vu",
        "co.ve" => "Venezuela - google.co.ve"
    );
    return $google_domains;
}