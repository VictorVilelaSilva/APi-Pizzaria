<?php

function badRequest(mixed $toEncode)
{
  header('Content-Type: application/json');
  header('HTTP/1.1 400 Bad Request');
  echo json_encode($toEncode);
  exit;
}
