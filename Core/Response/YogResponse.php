<?php
interface YogResponse
{
    public function getStatusCode();
    public function setStatusCode($status_code);
    public function setHeader($header, $value);
    public function getHeader($header);
    public function removeHeader($header);
    public function getBody();
    public function setBody($body);
    public function getHeaders();
    public function setHeaders(array $headers);
    public function Send();
    public function setSerializer($Serializer);
    public function getSerializer();
    public function isStatusError();
    public function setStatusReason($status_reason);
    public function getStatusReason();
    public function __toString();
    public function getProtocolVersion();
    public function setProtocolVersion($protocol_version);
    public function setCookie(YogCookie $Cookie);
    public function getCookie($name);
    public function getCookies();
    public function setSendBody($send_body);
}