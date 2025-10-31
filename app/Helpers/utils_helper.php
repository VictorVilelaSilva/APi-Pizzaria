<?php


function generate_uuid($uuid = null)
{
    if (getenv('CI_ENVIRONMENT') == 'development' and !is_null($uuid)) {
        return $uuid;
    }

    $uuid = array(
        'time_low'  => 0,
        'time_mid'  => 0,
        'time_hi'  => 0,
        'clock_seq_hi' => 0,
        'clock_seq_low' => 0,
        'node'   => array()
    );

    $uuid['time_low'] = mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16);
    $uuid['time_mid'] = mt_rand(0, 0xffff);
    $uuid['time_hi'] = (4 << 12) | (mt_rand(0, 0x1000));
    $uuid['clock_seq_hi'] = (1 << 7) | (mt_rand(0, 128));
    $uuid['clock_seq_low'] = mt_rand(0, 255);

    for ($i = 0; $i < 6; $i++) {
        $uuid['node'][$i] = mt_rand(0, 255);
    }

    $uuid = sprintf(
        '%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
        $uuid['time_low'],
        $uuid['time_mid'],
        $uuid['time_hi'],
        $uuid['clock_seq_hi'],
        $uuid['clock_seq_low'],
        $uuid['node'][0],
        $uuid['node'][1],
        $uuid['node'][2],
        $uuid['node'][3],
        $uuid['node'][4],
        $uuid['node'][5]
    );

    return $uuid;
}


function toASCII($string) {
    return iconv("UTF-8", "ASCII//TRANSLIT", $string);
}

/**
 * Summary of removeEspecials
 * @param mixed $string
 * @return array|string
 */
function removeEspecials($string)
{
    $what = array('ä', 'ã', 'à', 'á', 'â', 'ê', 'ë', 'è', 'é', 'ï', 'ì', 'í', 'ö', 'õ', 'ò', 'ó', 'ô', 'ü', 'ù', 'ú', 'û', 'À', 'Á', 'Ã', 'É', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'ú', 'ñ', 'Ñ', 'ç', 'Ç', ' ', '-', '(', ')', ',', ';', ':', '|', '!', '"', '#', '$', '%', '&', '/', '=', '?', '~', '^', '>', '<', 'ª', 'º', '.', '@');
    $by = array('a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'A', 'A', 'A', 'E', 'I', 'O', 'O', 'O', 'U', 'u', 'n', 'n', 'c', 'C', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
    return str_replace($what, $by, $string);
}

/**
 * Summary of removeSpecialCharactersFromEmail
 * @param mixed $string
 * @return array|string
 */
function removeSpecialCharactersFromEmail($string, $email = 'naoenviado@naoenviado.com.br')
{
    $what = array('ä', 'ã', 'à', 'á', 'â', 'ê', 'ë', 'è', 'é', 'ï', 'ì', 'í', 'ö', 'õ', 'ò', 'ó', 'ô', 'ü', 'ù', 'ú', 'û', 'À', 'Á', 'Ã','Â', 'É', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'ú', 'ñ', 'Ñ', 'ç', 'Ç', ' ', '-', '(', ')', ',', ';', ':', '|', '!', '"', '#', '$', '%', '&', '/', '=', '?', '~', '^', '>', '<', 'ª', 'º');
    $by = array('a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'A', 'A', 'A','A', 'E', 'I', 'O', 'O', 'O', 'U', 'u', 'n', 'n', 'c', 'C', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
    $string = str_replace($what, $by, $string);

    $valid = (bool) filter_var($string, FILTER_VALIDATE_EMAIL);

    if(!$valid){
        $string = $email;
    }

    return $string;
}

/**
 * Summary of removeEspecialsAccents
 * @param mixed $string
 * @return array|string
 */
function removeEspecialsAccents($string)
{
    $what = array('ä', 'ã', 'à', 'á', 'â', 'ê', 'ë', 'è', 'é', 'ï', 'ì', 'í', 'ö', 'õ', 'ò', 'ó', 'ô', 'ü', 'ù', 'ú', 'û', 'À', 'Á', 'Ã','Â', 'É', 'Í', 'Ó', 'Ô', 'Õ', 'Ú', 'ú', 'ñ', 'Ñ', 'ç', 'Ç');
    $by = array('a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'A', 'A', 'A','A', 'E', 'I', 'O', 'O', 'O', 'U', 'u', 'n', 'n', 'c', 'C');
    return str_replace($what, $by, $string);
}

/**
 * Summary of removeNumeric
 * @param mixed $valor
 * @return mixed
 */
function removeNumeric($valor)
{
    if (!is_string($valor)) {
        return $valor;
    }
    return preg_replace('/[0-9]/', '', $valor);
}


/**
 * Summary of removeNonNumeric
 * @param mixed $valor
 * @return mixed
 */
function removeNonNumeric($valor)
{
    if (!is_string($valor)) {
        return $valor;
    }
    return preg_replace('/\D/is', '', $valor);
}
/**
 * String para float
 * @param mixed $valor
 * @return float
 */
function convertStringToFloat($str)
{
    if (!is_string($str)) {
        return $str;
    }
    // Replace comma with a period for decimal points
    $str = str_replace('.', '', $str);
    $str = str_replace(',', '.', $str);

    // Remove all characters except digits, minus sign, and period
    $str = preg_replace('/[^0-9\.-]/', '', $str);

    // Convert to float
    return floatval($str);
}
/**
 * String para float
 * @param mixed $valor
 * @return float
 */
function convertStringToDouble($str)
{
    if (!is_string($str)) {
        return $str;
    }

    $commaPosition = strpos($str, ",");
    $dotPosition = strpos($str, ".");

    if ($commaPosition < $dotPosition) {
        $str = str_replace(",", "", $str);
        $str = str_replace(".", ",", $str);
    }

    // se nao tiver . ou , entao só dar o replace
    if (!strpos($str, '.') || !strpos($str, ',')) {
        $str = str_replace(',', '.', $str);

        // Remove all characters except digits, minus sign, and period
        $str = preg_replace('/[^0-9\.-]/', '', $str);

        return doubleval($str);
    }

    // Replace comma with a period for decimal points
    $str = str_replace('.', '', $str);
    $str = str_replace(',', '.', $str);

    // Remove all characters except digits, minus sign, and period
    $str = preg_replace('/[^0-9\.-]/', '', $str);

    return doubleval($str);
}

function dateToShow($valor)
{
    if (!$valor) return null;
    if ($valor instanceof \DateTime) return $valor->format("d/m/Y");
    if ($valor instanceof \CodeIgniter\I18n\Time) return $valor->toDateTime()->format("d/m/Y");
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $valor)) return $valor;
    return date("d/m/Y", strtotime($valor));
}

function dateToSave($valor)
{
    if (!$valor) return null;
    // get a y-m-d date and transform into d/m/Y
    return date("Y-m-d", strtotime($valor));
}

function dateTimeToShow($valor)
{
    if (!$valor) return null;
    // get a y-m-d date and transform into d/m/Y
    return date("d/m/Y H:i:s", strtotime($valor));
}

function unformatData($data)
{
    // get a d/m/Y date and transform into Ymd
    list($dia, $mes, $ano) = explode('/', $data);
    return $ano . $mes . $dia;
}

function moneyToShow($valor)
{
    $valor = convertStringToDouble($valor);
    // transform into R$ 0,00
    return "R$ " . number_format($valor, 2, ',', '.');
}

function percentToShow($valor, $decimal = null)
{
    $valor = convertStringToDouble($valor);
    // get the float number size
    if ($decimal === null) {
        $decimal = strlen(substr(strrchr($valor, "."), 1));
    }
    return number_format($valor, $decimal, ',', '.') . "%";
}

function cpfcnpjToShow($cpfcnpj)
{
    $cpfcnpj = removeNonNumeric($cpfcnpj);
    if (strlen($cpfcnpj) == 11) {
        return mask($cpfcnpj, '###.###.###-##');
    } else if (strlen($cpfcnpj) == 14) {
        return mask($cpfcnpj, '##.###.###/####-##');
    }
    return $cpfcnpj;
}

function formatarValor(string $valor): string
{
    $valor = str_replace(',', '.', $valor);

    $valorFormatado = number_format($valor, 2, ',', '.');

    return $valorFormatado;
}

function phoneToShow($phone, $ddd = true)
{
    $phone = removeNonNumeric($phone);
    if(!$phone) return '';
    if (strlen($phone) == 10 || strlen($phone) == 8) {
        if ($ddd) {
            return mask($phone, '(##) ####-####');
        }
        return mask($phone, '####-####');
    } else if (strlen($phone) == 11 || strlen($phone) == 9) {
        if ($ddd) {
            return mask($phone, '(##) #####-####');
        }
        return mask($phone, '#####-####');
    }
    return $phone;
}

/**
 * @param string $phone
 * return array [ddd, phone]
 */
function getDDDAndPhoneNumber(?string $phone)
{
    if (!$phone) return [null, null];
    $phone = removeNonNumeric($phone);
    return [substr($phone, 0, 2), substr($phone, 2)];
}

function cepToShow($cep)
{
    $cep = removeNonNumeric($cep);
    if (strlen($cep) == 8) {
        return mask($cep, '#####-###');
    }
    return $cep;
}



function mask($val, $mask)
{
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; $i++) {
        if ($mask[$i] == '#') {
            if (isset($val[$k])) {
                $maskared .= $val[$k++];
            }
        } else {
            if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
        }
    }
    return $maskared;
}

/**
 * Summary of removeNonAlphabetic
 * @param mixed $valor
 * @return mixed
 */
function removeNonAlphabetic($valor)
{
    if (!is_string($valor)) {
        return $valor;
    }

    $valor = removeEspecials($valor);
    $valor = removeNumeric($valor);
    return $valor;
}

/**
 * Summary of generate_now
 * @return mixed
 */
function generate_now($moreDays = null)
{
    $data = date("Y-m-d H:i:s");
    if ($moreDays != null) {
        $data = date("Y-m-d H:i:s", strtotime("+$moreDays days"));
    }
    return $data;
}

function dateDiffFormat($startDate, $endDate)
{
    return date_diff(DateTime::createFromFormat('Y-m-d', $startDate), DateTime::createFromFormat('Y-m-d', $endDate));
}

function calculateExpiredRegistration($startDate, $endDate)
{
    return dateDiffFormat($startDate, $endDate)->invert == 0 ? true : false;
}

function extenso($days)
{
    $formatter = new NumberFormatter('PT_BR', NumberFormatter::SPELLOUT);

    return $formatter->format($days);
}

function alertExpiredRegistration($startDate, $endDate, $days)
{

    $dateDiff = dateDiffFormat($startDate, $endDate);
    $dateDiffDays = $dateDiff->days;
    $expired = calculateExpiredRegistration($startDate, $endDate);

    $message = "";
    if ($expired) {
        $message = "Cadastro vencido.";
    } elseif (($dateDiffDays <= $days) and ($dateDiffDays == 1)) {
        $message = "Este cadastro vencerá amanhã.";
    } elseif ($dateDiffDays <= $days) {
        $message = "Este cadastro vencerá em __days__ dias.";
        $message = str_replace("__days__", $dateDiffDays, $message);
    }

    return $message;
}

function base64url_encode(string $data): string
{
    $base64Url = strtr(base64_encode($data), '+/', '-_');

    return rtrim($base64Url, '=');
}

function base64url_decode(string $base64Url): string
{
    return base64_decode(strtr($base64Url, '-_', '+/'));
}

function isCpfSize($value)
{
    return strlen(removeNonNumeric($value)) == 11;
}
function isCnpjSize($value)
{
    return strlen(removeNonNumeric($value)) == 14;
}

function validar_cpf($cpf)
{
    $retorno = false;

    $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);
    //CPF 00000000000 é para simulacao
    if (
        $cpf == '11111111111' ||
        $cpf == '22222222222' ||
        $cpf == '33333333333' ||
        $cpf == '44444444444' ||
        $cpf == '55555555555' ||
        $cpf == '66666666666' ||
        $cpf == '77777777777' ||
        $cpf == '88888888888' ||
        $cpf == '99999999999'
    ) {
        return $retorno;
    }


    // Valida tamanho
    if (strlen($cpf) != 11) {
        return $retorno;
    }
    // Calcula e confere primeiro d�gito verificador
    for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--)
        $soma += $cpf[$i] * $j;
    $resto = $soma % 11;
    if ($cpf[9] != ($resto < 2 ? 0 : 11 - $resto)) {

        return $retorno;
    }
    // Calcula e confere segundo d�gito verificador
    for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--)
        $soma += $cpf[$i] * $j;
    $resto = $soma % 11;

    return $cpf[10] == ($resto < 2 ? 0 : 11 - $resto);
}

function validar_cnpj($cnpj)
{

    $retorno = false;


    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
    // Valida tamanho
    if (strlen($cnpj) != 14) {
        return $retorno;
    }
    // Valida primeiro d�gito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;

    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
        return $retorno;
    }
    // Valida segundo d�gito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}

function validar_cpfcnpj($string)
{
    $retorno = false;
    if (strlen($string) == 11) {
        $retorno = validar_cpf($string);
    } else if (strlen($string) == 14) {
        $retorno = validar_cnpj($string);
    }

    return $retorno;
}

function getModality($m)
{
    $modalidade = substr($m, 0, 2);

    $vet_mod = [
        '01' => 'Adiantamentos a depositantes',
        '02' => 'Empréstimos',
        '03' => 'Títulos descontado Direitos creditorios descontados',
        '04' => 'Financiamentos',
        '05' => 'Financiamentos à exportação',
        '06' => 'Financiamentos à importaçao',
        '07' => 'Financiamentos com interveniência',
        '08' => 'Financiamentos rurais e agroindustriais',
        '09' => 'Financiamentos imobiliários',
        '10' => 'Financiamentos de títulos e valores imobilários',
        '11' => 'Financiamentos de infraestrutura e desenvolvimento',
        '12' => 'Operações de arrendamento',
        '13' => 'Outros créditos',
        '14' => 'Repasses interfinanceiros, Relaçoes interfinanceiras',
        '15' => 'Coobrigações',
        '18' => 'Títulos de crédito (fora da carteira classificada)',
        '19' => 'Limite',
        '20' => 'Retenção de risco'
    ];

    $vet_submod = [
        "0101" => "Adiantamentos a depositantes",
        "0201" => "Cheque especial e conta garantida",
        "0202" => "Crédito pessoal - com consignação em folha de pagam.",
        "0203" => "Crédito pessoal - sem consignação em folha de pagam.",
        "0204" => "Crédito rotativo vinculado a cartão de crédito",
        "0205" => "Capital de giro com prazo de vencimento inferior a 30 dias",
        "0206" => "Capital de giro com prazo vencim. igual ou superior 30 dias",
        "0207" => "Vendor",
        "0208" => "Compror",
        "0209" => "ARO - adiantamento de receitas orçamentárias",
        "0210" => "Cartão de crédito - compra, fatura parcelada ou saque financiado pela instituição emitente do cartão",
        "0211" => "Home equity",
        "0212" => "Microcrédito",
        "0213" => "Cheque especial",
        "0214" => "Conta garantida",
        "0215" => "Capital de giro com prazo de vencimento até 365 dias",
        "0216" => "Capital de giro com prazo vencimento superior 365 dias",
        "0217" => "Capital de giro com teto rotativo",
        "0218" => "Cartão de crédito - não migrado",
        "0250" => "Recebíveis adquiridos",
        "0290" => "Financiamento de projeto",
        "0299" => "Outros empréstimos",
        "0301" => "Desconto de duplicatas",
        "0302" => "Desconto de cheques",
        "0303" => "Antecipação de fatura de cartão de crédito",
        "0398" => "Outros direitos creditórios descontados",
        "0399" => "Outros títulos descontados",
        "0401" => "Aquisição de bens - veículos automotores",
        "0402" => "Aquisição de bens - outros bens",
        "0403" => "Microcrédito",
        "0404" => "Vendor",
        "0405" => "Vompror",
        "0406" => "Cartão de crédito - compra ou fatura parcelada pela instituição financeira emitente do cartão",
        "0407" => "Aquisição de bens - veículos automotores acima de 2 toneladas",
        "0499" => "Outros financiamentos",
        "0440" => "Financiamentos agroindustriais",
        "0450" => "Recebíveis adquiridos",
        "0490" => "Financiamento de projeto",
        "0501" => "Financiamento a exportação",
        "0502" => "Adiantamento sobre contratos de câmbio",
        "0503" => "Adiantamento sobre cambiais entregues",
        "0504" => "Cred decorrentes de contratos de exportação-export note",
        "0590" => "Financiamento de projeto",
        "0599" => "Outros financiamentos a exportação",
        "0601" => "Financiamento a importação",
        "0690" => "Financiamento de projeto",
        "0701" => "Aquisição de bens com interveniência - veículos autom.",
        "0702" => "Aquisiça o de bens com interveniência - outros bens",
        "0790" => "Financiamento de projeto",
        "0799" => "Outros financiamentos com interveniência",
        "0801" => "Custeio e pré-custeio",
        "0802" => "Investimento e capital de giro de financiam. agroindustr.",
        "0803" => "Comercializaça o e pré-comercialização",
        "0804" => "Industrialização",
        "0890" => "Financiamento de projeto",
        "0901" => "Financiamento habitacional - SFH",
        "0902" => "Financiamento habitacional - exceto SFH",
        "0903" => "Financiamento imobiliário - empreendim, exceto habitac.",
        "0990" => "Financiamento de projeto",
        "1001" => "Financiamento de TVM",
        "1101" => "Financiamento de infraestrutura e desenvolvimento",
        "1190" => "Financiamento de projeto",
        "1201" => "Arrendamento financeiro exceto veículos automotores e imóveis",
        "1202" => "Arrendamento financeiro imobiliário",
        "1203" => "Subarrendamento",
        "1205" => "Arrendamento operacional",
        "1206" => "Arrendamento financeiro de veículos automotores",
        "1207" => "Arrendamento financeiro de veículos automotores acima",
        "1290" => "Financiamento de projeto",
        "1301" => "Avais e fianças honrados",
        "1302" => "Devedores por compra de valores e bens",
        "1303" => "Títulos e cre ditos a receber",
        "1304" => "Cartão de crédito - compra à vista e parcelado lojista",
        "1350" => "Recebíveis adquiridos",
        "1390" => "Financiamento de projeto",
        "1399" => "Outros com característica de crédito",
        "1401" => "Repasses interfinanceiros",
        "1402" => "Recebíveis de arranjo de pagamento",
        "1403" => "Outros valores a receber relativos a transações de pagamento",
        "1501" => "Beneficiários de garantias prestadas para operações com PJ financeira",
        "1502" => "Beneficiários de garantias prestadas para operações com outras pessoas",
        "1503" => "Beneficiários de garantias prestadas para fundos constitucionais",
        "1504" => "Beneficiários de garantias prestadas para participação em processo licitatório",
        "1505" => "Carta de crédito de importação",
        "1511" => "Coobrigação assumida em cessa o com coobrigação para pessoa integrante do SFN",
        "1512" => "Coobrigação assumida em cessa o com coobrigação para pessoa na o integrante do SFN, inclusive securitizadora e fundos de investimento",
        "1590" => "Financiamento de projeto",
        "1599" => "Beneficiários de outras garantias prestadas",
        "1513" => "Beneficiários de outras coobrigações",
        "1801" => "CPR - Cédula de Produto Rural",
        "1802" => "EN - Nota de Exportação",
        "1803" => "Debéntures",
        "1899" => "Outros",
        "1901" => "Limite contratado e não utilizado",
		"1902" => "Cheque especial",
		"1903" => "Conta garantida",
		"1904" => "Cartão de Crédito",
		"1905" => "Capital de giro",
		"1906" => "Crédito pessoal",
		"1907" => "Vendor",
		"1908" => "Compror",
		"1909" => "Descontos",
		"1910" => "Aquisição",
        "1999" => "Outros",
        "2001" => "Retenção de risco assumida por aquisiça o de cotas de fundos",
        "2002" => "Retenção de risco assumida por aquisição de instrumentos com lastros em operações de crédito"
    ];

    return [
        "title"     => $vet_mod[$modalidade] ?? '',
        "subtitle"  => $vet_submod[$m] ?? ''
    ];
}

function variacaoCambial($v)
{
    return (strtoupper($v) === 'N') ? "Não" : "Sim";
}

function valorRS($valor)
{
    return "R$ " . number_format(sprintf("%01.2f", $valor), 2, ',', '.');
}

function porcentagem($valor)
{
    return   number_format(sprintf("%01.2f", $valor), 2, ',', '.') . "%";
}

function dataBase($d, bool $monthsInFull = false)
{
    $meses = array("", "Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez");
    if ($monthsInFull) {
        $meses = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    }
    $data = explode("-", $d);
    return $meses[(int)$data[1]] . "/" . substr($data[0], -2);
}

function getReferenceVetor(int $positionVetor)
{
    $ReferenceVetor = [
        110 => 30,
        120 => 60,
        130 => 90,
        140 => 180,
        150 => 360,
        160 => 720,
        165 => 1080,
        170 => 1440,
        175 => 1800,
        180 => 5400,
        190 => '...',
        199 => 'indet.',

        205 => 14,
        210 => 30,
        220 => 60,
        230 => 90,
        240 => 120,
        245 => 150,
        250 => 180,
        255 => 240,
        260 => 300,
        270 => 360,
        280 => 540,
        290 => '...',
        310 => 'até 12 meses',
        320 => 'acima de 12 a 48 meses',
        330 => 'acima de 48 meses'
    ];

    return $ReferenceVetor[$positionVetor] ?? [];
}

function getReferenceVetorModality(int $positionVetor)
{
    $vetorModality = [
        2     => 'loans',
        4     => 'financing',
        5     => 'financing',
        6     => 'financing',
        7     => 'financing',
        8     => 'financing',
        9     => 'financing',
        10    => 'financing',
        11    => 'financing',
    ];

    return $vetorModality[$positionVetor] ?? "others";
}

function getCustomizationModality(string $positionVetor, string $type): string
{
    $vetorModality = [
        "loans"     => [
            "label" => "Empréstimos",
            "icon"  => "mdi:trending-up",
            "avatarColor" => "success"
        ],
        "financing" => [
            "label" => "Financiamentos",
            "icon"  => "mdi:currency-usd",
            "avatarColor" => "warning"
        ],
        "others" => [
            "label" => "Outros",
            "icon"  => "mdi:poll",
            "avatarColor" => "info"
        ]
    ];

    return $vetorModality[$positionVetor][$type] ?? "";
}

function getDescription($n): string
{
    $referenceVetorDescription = [
        20 => 'Limite com vencimento até 360 dias',
        40 => 'Limite com vencimento acima de 360 dias',
        60 => 'A liberar até 360 dias',
        80 => 'A liberar acima de 360 dias',
        110 => 'A vencer até 30 dias',
        120 => 'A vencer de 31 a 60 dias',
        130 => 'A vencer de 61 a 90 dias',
        140 => 'A vencer de 91 a 180 dias',
        150 => 'A vencer de 181 a 360 dias',
        160 => 'A vencer de 361 a 720 dias',
        165 => 'A vencer de 721 a 1080 dias',
        170 => 'A vencer de 1081 a 1440 dias',
        175 => 'A vencer de 1441 a 1800 dias',
        180 => 'A vencer de 1801 a 5400 dias',
        190 => 'A vencer acima de 5400 dias',
        199 => 'A vencer com prazo indeterminado',
        205 => 'Vencidos de 1 a 14 dias',
        210 => 'Vencidos de 15 a 30 dias',
        220 => 'Vencidos de 31 a 60 dias',
        230 => 'Vencidos de 61 a 90 dias',
        240 => 'Vencidos de 91 a 120 dias',
        245 => 'Vencidos de 121 a 150 dias',
        250 => 'Vencidos de 151 a 180 dias',
        255 => 'Vencidos de 181 a 240 dias',
        260 => 'Vencidos de 241 a 300 dias',
        270 => 'Vencidos de 301 a 360 dias',
        280 => 'Vencidos de 361 a 540 dias',
        290 => 'Vencidos acima de 540 dias',
        310 => 'Prejuízo até 12 meses',
        320 => 'Prejuízo acima de 12 a 48 meses',
        330 => 'Prejuízo acima de 48 meses'
    ];

    return $referenceVetorDescription[$n];
}

function getRiskRating(int $reference): string
{
    $referenceVetorDescription = [
        20  => 'A',
        40  => 'A',
        60  => 'A',
        80  => 'A',
        110 => 'A',
        120 => 'A',
        130 => 'A',
        140 => 'A',
        150 => 'A',
        160 => 'A',
        165 => 'A',
        170 => 'A',
        175 => 'A',
        180 => 'A',
        190 => 'A',
        199 => 'A',
        205 => 'A',
        210 => 'B',
        220 => 'C',
        230 => 'D',
        240 => 'E',
        245 => 'F',
        250 => 'G',
        255 => 'H',
        260 => 'H',
        270 => 'H',
        280 => 'H',
        290 => 'H',
        310 => 'HH',
        320 => 'HH',
        330 => 'HH'
    ];

    return $referenceVetorDescription[$reference] ?? '';
}

function getPersonType($cpfcnpj)
{
    // TODO PJ (Pessoa Jurídica), PF (Pessoa Física) ou JS (Jurídica Simples)
    return strlen($cpfcnpj) == 11 ? 'PF' : 'PJ';
}

function maskSensitiveData(?array $data): ?array
{
    if (empty($data)) {
        return [];
    }

    $aux = [];
    $filter = ["senha", "token", "code", "pass", "password", "authorization", "base64_pdf", "base64"];

    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $aux[$key] = maskSensitiveData($value);
            continue;
        }

        if (hasAnyWord($filter, $key)) {
            if ($key === 'authorization') {
                $valueBearer = explode(" ", $value);
                if (count($valueBearer) >= 2) {
                    $maskBearer = preg_replace('/\B[^@.]/', '*', $valueBearer[1]);
                    $value = "$valueBearer[0] $maskBearer";
                }
                $aux[$key] = $value;
                continue;
            }
            $value = "*";
        }

        $aux[$key] = $value;
    }

    return $aux;
}
// first due ddate igual ao due_date
function hasAnyWord(array $words, string $haystack): bool
{
    if (in_array($haystack, ['status_code', 'code', 'companyCode', 'feeCode'])) {
        return false;
    }

    foreach ($words as $value) {
        if (strpos(strtolower($haystack), strtolower($value)) !== false) {
            return true;
        }
    }

    return false;
}

function maskDocumentId($documentId)
{
    $documentId = removeMasks($documentId);
    switch (strlen($documentId)) {
        case 11:
            return substr($documentId, 0, 3) . "." . substr($documentId, 3, 3) . "." . substr($documentId, 6, 3) . "-" . substr($documentId, 9);
        case 14:
            return substr($documentId, 0, 2) . "." . substr($documentId, 2, 3) . "." . substr($documentId, 5, 3) . "/" . substr($documentId, 8, 4) . "-" . substr($documentId, 12);
    }
    return $documentId;
}

function removeMasks($string)
{
    $string = str_replace('.', '', $string);
    $string = str_replace('/', '', $string);
    $string = str_replace('-', '', $string);
    $string = str_replace('(', '', $string);
    $string = str_replace(')', '', $string);
    $string = str_replace('R$', '', $string);
    $string = str_replace(' ', '', $string);

    return $string;
}

function getReferenceVetorLabelTable(int $positionVetor)
{
    $labelVetor = [
        110 => "30 Dias",
        120 => "31 a 60 Dias",
        130 => "61 a 90 Dias",
        140 => "91 a 180 Dias",
        150 => "181 a 360 Dias",
        160 => "361 a 720 Dias",
        165 => "721 a 1080 Dias",
        170 => "1081 a 1440 Dias",
        175 => "1441 a 1800 Dias",
        180 => "1801 a 5400 Dias",
        190 => '5401 a ...',
        199 => 'indet.',

        205 => "14 Dias",
        210 => "15 a 30 Dias",
        220 => "31 a 60 Dias",
        230 => "61 a 90 Dias",
        240 => "91 a 120 Dias",
        245 => "121 a 150 Dias",
        250 => "151 a 180 Dias",
        255 => "181 a 240 Dias",
        260 => "241 a 300 Dias",
        270 => "301 a 360 Dias",
        280 => "361 a 540 Dias",
        290 => '541 a ...',
        310 => 'até 12 meses',
        320 => 'acima de 12 a 48 meses',
        330 => 'acima de 48 meses'
    ];

    return $labelVetor[$positionVetor] ?? [];
}

function isProduction(): bool
{
    return in_array(getenv('CI_ENVIRONMENT'), ['production']);
}

function getTypeUser(string $cpfCnpj): string
{
    $map = [
        11 => "name",
        14 => "company_name"
    ];

    return @$map[strlen(removeMasks($cpfCnpj))] ?? "";
}
function getLastMonths(string $date, int $numberMonths): array
{
    $dateObject = DateTime::createFromFormat('Y-m', $date);
    $months = [];
    if (!$dateObject) {
        return [];
    }

    for ($i = 0; $i < $numberMonths; $i++) {
        $months[] = $dateObject->format('Y-m');
        $dateObject->modify('-1 month');
    }

    $months = array_reverse($months);
    array_pop($months);
    return $months;
}

function getLastMonthsV2(string $date, int $numberMonths): array
{
    $dateObject = DateTime::createFromFormat('Y-m', $date);
    $months = [];
    if (!$dateObject) {
        return [];
    }

    for ($i = 0; $i < $numberMonths; $i++) {
        $dateObject->modify('-1 month');
        $months[] = $dateObject->format('Y-m');
    }

    $months = array_reverse($months);
    array_pop($months);
    return $months;
}

function getBoolean($value)
{
    if (is_string($value)) {
        return strtoupper($value) == "S" || filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    return (bool) $value;
}

function getYesOrNo($value)
{
    if (is_string($value)) {
        // TRUE for "S", "1", "true", "on" and "yes"
        $value = strtoupper($value) == "S" || filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    if ($value) return "( ) Não (X) Sim";

    return "(x) Não ( ) Sim";
}

function snakeToCamelCase(string $string, bool $capitalizeFirstCharacter = false)
{
    $string = strtolower($string);

    $str = str_replace('_', '', ucwords($string, '_'));

    if (!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }

    return $str;
}

function convertKeysToCamelCase(mixed $apiResponseArrays)
{
    $arr = [];

    foreach ($apiResponseArrays as $key => $value) {
        $key = snakeToCamelCase($key);

        if (is_object($value) || is_array($value)) {
            $value = convertKeysToCamelCase($value);
        }

        $arr[$key] = $value;
    }
    return $arr;
}

function camelToSnakeCase(string $string): string
{
    return strtolower(preg_replace(["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"], ["_$1", "_$1_$2"], lcfirst($string)));
}

function generateRandonPassword(int $length = 6, bool $specials = true, bool $numbers = true): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = substr(str_shuffle($chars), 0, $length);

    if ($numbers) {
        $numbers = '0123456789';
        $password = $password . substr(str_shuffle($numbers), 0, 1);
    }

    if ($specials) {
        $specials = '!*#%&@$?';
        $password = $password . substr(str_shuffle($specials), 0, 1);
    }

    return $password;
}

function getPersonName($person)
{

    if (isCpfSize($person['document_id'])) {
        return $person['name'];
    }

    if (isCnpjSize($person['document_id'])) {
        return $person['company_name'];
    }

    return $person['name'] ?? $person['company_name'];
}

function getPersonDate($person)
{

    if (isCpfSize($person['document_id'])) {
        return $person['date_birthday'];
    }

    if (isCnpjSize($person['document_id'])) {
        return $person['date_company_foundation'];
    }

    return $person['date_birthday'] ?? $person['date_company_foundation'];
}

function getPersonBudget($person)
{
    return $person['salary'] + $person['other_income']  + $person['gross_income'];
}

function getClientLimit($client)
{
    return $client['limit'] - $client['used_limit'];
}

function getClientConsig($client, $person)
{

    $salary = getPersonBudget($person);
    if ($client['used_limit']) return 100;
    if ($salary == 0) return 0;
    $margin = ($client['used_limit'] / $salary) * 100; // TODO conferir used_limit para ser valor mensal (pois salário é mensal)

    return $margin;
}

function generateExternalCode(array $data)
{
    $uuid1 = explode("-", $data['uuid'])[1];
    $dataUpdate['code'] = strtoupper($uuid1 . "-" . $data['id']);
    return $dataUpdate['code'];
}


function vencimento($primeirovenc, $parcatual, $diaspvenc = 0, $periodo = 0)
{
    $mesespvenc = $parcatual - 1;
    $venc = new DateTime($primeirovenc);

    if ($periodo > 0 && $periodo != 30) {
        $periodo = $diaspvenc + ($periodo * ($parcatual - 1));
        $venc->add(new DateInterval("P{$periodo}D")); // adds Day
    } else {
        if ($diaspvenc > 0) {
            $venc->add(new DateInterval("P{$diaspvenc}D")); // adds Day
        } else {
            $venc->add(new DateInterval("P{$mesespvenc}M")); // add Month
        }

    }


    return $venc->format('Y-m-d');
}
function isUUID($value) {
    return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $value);
}

function firstValue(...$values) {
    foreach ($values as $value)
        if ($value) return $value;
    return 0;
}

function joinAssocArrayWithDelimiter($assocArray, $ignoreFirstDelimiter = true)
{
  // Initialize an empty array to store valid keys
  $validParts = [];

  $count = 0;
  // Loop through the associative array
  foreach ($assocArray as $key => $delimiter) {
    // Check if the key and the delimiter are not empty
    if (!empty($key)) {
      $count++;
      // Append the key and the delimiter to the valid parts array
      $validParts[] = $ignoreFirstDelimiter && $count == 1 ? $key : ($delimiter . $key);
    }
  }

  // Join the valid parts array into a single string
  $result = implode('', $validParts);

  return $result;
}
function abbreviateCompanyName(string $name)
{
    $split_name = explode(" ", $name);
    if (count($split_name) > 2) {
        for ($i = 2; (count($split_name) - 1) > $i; $i++) {
            if (strlen($split_name[$i]) > 3) {
                $split_name[$i] = substr($split_name[$i], 0, 1) . ".";
            }
        }
    }
    return implode(" ", $split_name);
}

function acronym(string $longname): string
{
    $letters = [];
    $words   = explode(' ', $longname);
    foreach($words as $word) {
        $word = (substr($word, 0, 1));
        array_push($letters, $word);
    }
    $shortname = strtoupper(implode($letters));
    return $shortname;
}

function getCompleteAddress(array $data) : string{
    return joinAssocArrayWithDelimiter([
        $data['street_address'] => "",
        $data['address_number'] => ", ",
        $data['address_line'] => ", ",
        $data['neighborhood'] => ", ",
        $data['city'] => ", ",
        $data['id_state'] => "-",
        $data['zip_code'] => ", "
    ]);
}

function sumSecondsInDateTime($datetime, $seconds){
    return date('Y-m-d H:i:s', strtotime($datetime) + $seconds);
}
function getCpfCnpjLabel(string $cpfCnpj): string
    {
        $map = [
            11 => "CPF",
            14 => "CNPJ"
        ];
        return @$map[strlen(removeMasks($cpfCnpj))] ?? "";
    }

function removeMasksFilter($string)
{
    $string = str_replace('.', '', $string);
    $string = str_replace('/', '', $string);
    $string = str_replace('-', '', $string);
    $string = str_replace('(', '', $string);
    $string = str_replace(')', '', $string);
    $string = str_replace('R$', '', $string);

    return $string;
}

function getCpfOrCnpjFromRemittance($cpfCnpj) {
    if(!$cpfCnpj) return null;
    if (substr($cpfCnpj, 0, 4) === '0000')
        return substr($cpfCnpj, 4); // Remove os 4 zeros obrigatórios do CPF
    return substr($cpfCnpj, 1); // Remove o 1 zero obrigatório do CNPJ
}

function getYesOrNoObject(?string $value): array {
    if($value === "S") {
        return [
            "id" => "S",
            "description" => "Sim",
        ];
    }
    return [
        "id" => "N",
        "description" => "Não",
    ];
}

/**
 * Summary of remove55InPhone
 * @param mixed $valor
 * @return mixed
 */
function remove55InPhone($valor)
{
    $valor = preg_replace('/\D/is', '', $valor);

    if (isset($valor) && strlen($valor) > 11) {
        $valor = substr($valor, 2);
    }

    return $valor;
}

function updateAddressNumber(string $hasNoNumber, string|null $addressNumber)
{
    if ($hasNoNumber == 'S') {
        $addressNumber = "S/N";

        return $addressNumber;
    }

    return ($addressNumber);
}

function removeSpecialChars($string) {
    // Remove todos os caracteres que não são letras, números ou espaços
    return preg_replace('/[^A-Za-z0-9\s]/', '', $string);
}

function hourToShow($hour) {
    $hour = explode(" ", $hour);
    $hour = explode(":", $hour[1]);
    return $hour[0] . ":" . $hour[1] . ":" . $hour[2];
}

function compareArrays(array $oldData, array $newData): array
{
    $oldData = array_filter($oldData, fn($value) => !is_null($value));
    $newData = array_filter($newData, fn($value) => !is_null($value));

    $diff = array_diff_assoc($newData, $oldData);

    return $diff;
}

function alertAverbation($startDate, $endDate, $days)
{
    $dateDiff = dateDiffFormat($startDate, $endDate);
    $dateDiffDays = $dateDiff->days;
    $expired = calculateExpiredRegistration($startDate, $endDate);

    $message = null;
    if ($expired) {
        $message = "Você não pode mais averbar essa operação. A data era até o dia " . dateToShow($startDate) . ".";
    }
    else if ($dateDiffDays <= $days) {
        $message = "Você tem até o dia " . dateToShow($startDate) . " para averbar essa operação.";
    }

    return $message;
}

function alertCount($count)
{
    $message = null;
    if ($count != null) {
        $message = "A UC fornecida tem a operação {$count} vigente.";
    }

    return $message;
}

function bufferToString(array $columns): string
{
    $bufferString = '';
    foreach ($columns as $column){
        if (empty($column)) $bufferString .= "\n";
        else $bufferString .= substr(str_pad($column[0], $column[1], $column[2], $column[3] ?? STR_PAD_LEFT), 0, $column[1]);
    }

    return $bufferString;
}

function buildRateArray(array $group): array {
    $result = [];
    foreach ($group as $name => $value) {
        $result[] = [
            'name' => camelToSnakeCase($name),
            'value' => convertStringToDouble($value),
        ];
    }
    return $result;
}
function handleBaseDateInitialAndFinal($baseDataInitial, $baseDataFinal)
{
    // Cria objetos DateTime para ambas as datas
    $datetimeInitial = date_create($baseDataInitial);
    $datetimeFinal = date_create($baseDataFinal);

    // Verifica se as datas são válidas
    if (!$datetimeInitial || !$datetimeFinal) {
        return "$baseDataInitial,$baseDataFinal"; // Retorna original se inválido
    }

    // Calcula a diferença entre as datas
    $interval = date_diff($datetimeInitial, $datetimeFinal);
    $monthsDifference = $interval->y * 12 + $interval->m;

    // Se a diferença for menor ou igual a 12 meses, retorna as originais
    if (abs($monthsDifference) <= 12) {
        return "$baseDataInitial,$baseDataFinal";
    }

    // Ajusta a data mais antiga para ficar dentro de 12 meses
    if ($monthsDifference > 12) {
        $adjustedDate = clone $datetimeFinal;
        $adjustedDate->modify('-12 months');
        $adjustedDateStr = $adjustedDate->format('Y-m');
        return "$adjustedDateStr,$baseDataFinal";
    } else {
        $adjustedDate = clone $datetimeInitial;
        $adjustedDate->modify('-12 months');
        $adjustedDateStr = $adjustedDate->format('Y-m');
        return "$baseDataInitial,$adjustedDateStr";
    }
}

function getTypePersonPFOrPJByDocumentId($documentId)
{
    return  isCnpjSize($documentId) ? 'pj' : 'pf';
}

function getVisibleOrNotObject(?string $value): array {
    if($value === "S") {
        return [
            "id" => "S",
            "description" => "Visível",
        ];
    }
    return [
        "id" => "N",
        "description" => "Oculto",
    ];
}