<?php



function calculerMoyenne($interro, $devoir)
{
    return round(((float)$interro * 0.30) + ((float)$devoir * 0.70), 2);
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

function flash($key, $message = null)
{
    if ($message === null) {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    $_SESSION['flash'][$key] = $message;
}
