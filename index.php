<?php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="de" xml:lang="de">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="robots" content="noindex,nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TUBS Account Query</title>
        <meta name="description" content="internal website">
        <style>
            :root {
                --formWidth: 200pt;
            }
            @media (prefers-color-scheme: light) {
                :root {
                    --color: black;
                    --colorL: #555;
                    --bg: white;
                }
            }
            @media (prefers-color-scheme: dark) {
                :root {
                    --color: #eee;
                    --colorL: #aaa;
                    --bg: black;
                }
            }
            @font-face {
                font-family: Manrope;
                src: url(Manrope-VariableFont_wght.ttf);
            }
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
            body {
                display: flex;
                font-family: Manrope, sans-serif;
                color: var(--color);
                background: var(--bg);
            }
            body > div {
                margin: auto;
                text-align: center;
                padding: 50pt 10pt;
            }
            body > div > * {
                text-align: left;
            }

            #query-form {
                position: sticky;
                background: var(--bg);
                top: 0;
                text-align: center;
            }
            form {
                display: inline-block;
                width: var(--formWidth);
            }
            form input {
                color: var(--color);
                background: var(--bg);
            }
            form input:focus {
                outline: none;
            }
            form input[type="text"] {
                font-size: 120%;
                padding: 5pt;
                border: none;
                border-bottom: 1px solid #8883;
                width: calc(100% - 50pt);
            }
            form input[type="text"]:hover {
                border-bottom: 1px solid var(--color);
            }
            form input[type="submit"] {
                padding: 10pt;
                border: none;
                border-radius: 50%;
            }
            form input[type="submit"]:hover {
                background: #8881;
            }

            #hints {
                color: #888;
                font-size: 90%;
                margin: 10pt 0 20pt;
                display: inline-block;
                width: var(--formWidth);
            }

            #results .entry {
                padding: 20pt 0;
            }
            #results .entry:not(:first-child) {
                border-top: 1px solid #8882;
            }
            #results .entry > i {
                display: block;
                text-align: center;
            }
            #results .row {
                margin-top: 10pt;
            }
            #results .row > div {
                box-sizing: border-box;
                vertical-align: top;
            }
            #results .row > div:first-child {
                color: var(--colorL);
                font-size: 80%;
                vertical-align: baseline;
            }
            #results .row > div:last-child {
                vertical-align: top;
            }
            @media only screen and (min-width: 600px) {
                #results .row {
                    margin-top: unset;
                }
                #results .row > div {
                    display: inline-block;
                }
                #results .row > div:first-child {
                    width: 40%;
                    text-align: right;
                    padding-right: 20pt;
                }
                #results .row > div:last-child {
                    width: 60%;
                }
            }
        </style>
    </head>
    <body>
        <div>
            <div id="query-form">
                <form method="GET" autocomplete="off">
		<input type="text" name="q" value="<?=$_GET['q']?>" placeholder="Search TUBS people">
                    <input type="submit" value="&#x1F50D;">
                </form>
            </div>
            <div id="hints">
                Examples:<br>
                <code>
			uid=maxmuste<br>
			uid=y0012345<br>
			cn=*unclear name part*<br>
			givenname=first name<br>
			sn=last name<br>
			telephonenumber=*2109
                </code>
            </div>
            <div id="results">
<?php
if (isset($_GET['q'])) {
    // Verbindung zum LDAP Server
    $conn = ldap_connect($ldap_server, $ldap_port);
    if (!$conn)
        return 'Wir scheinen Verbindungsprobleme zu haben. Versuche es bitte später erneut.';
    else {
        if (!ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3))
            return 'LDAP Protokoll Version 3 ist nicht verfügbar.';
        else {
            // Nutzerinformationen vom LDAP Server abragen
            $search_result = ldap_search($conn, $ldap_base_dn, $_GET['q']);
            if ($search_result === false) {
                echo '<div class="entry"><i>Invalid query.</i></div>';
            }
            else {
                $user_data = ldap_get_entries($conn, $search_result);
                if (!$user_data['count']) {
                    echo '<div class="entry"><i>No matches.</i></div>';
                }
                else {
                    foreach ($user_data as $data_idx => $data) {
                        if ($data_idx === 'count')
                            continue;
                        $data = array_filter($data, function(&$attr) { return is_array($attr); });
                        $data = array_map(function(&$attr)
                            {
                                if ($attr['count'] > 1)
                                    return implode(' - ', array_filter($attr, function(&$attr_key) { return $attr_key !== 'count'; }, ARRAY_FILTER_USE_KEY));
                                else
                                    return $attr[0];
                            }, $data);

                        echo '<div class="entry">';
                        foreach ($data as $key => $value) {
                            echo '<div class="row"><div>' . $key . '</div><div>' . $value . '</div></div>';
                        }
                        echo '</div>';
                    }
                }
            }
        }
    }
}
?>
            </div>
        </div>
    </body>
</html>
