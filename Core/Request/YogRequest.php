<?php
interface YogRequest
{
    const method_GET        = 'GET';
    const method_POST       = 'POST';
    const method_PUT        = 'PUT';
    const method_DELETE     = 'DELETE';
    const method_HEAD       = 'HEAD';
    const method_OPTIONS    = 'OPTIONS';
    const method_TRACE      = 'TRACE';
    const method_CONNECTION = 'CONNECTION';
    public function BuildFromInput();
    public function getHeaders($key = null);
    public function setHeader($header, $value);
    public function removeHeader($header);
    public function getMethod();
    public function setMethod($method);
    public function getData($key = null);
    public function setData($value);
    public function setDataKey($key, $value);
    public function getAccept();
    public function SetAccept($accept);
    public function isAcceptable($type);
    public function Send($URL, YogResponse $Response = NULL);
    public function setProtocolVersion($protocol_version = '1.0');
    public function addCookie(YogCookie $Cookie);
    public function removeCookie($name);
    public function getCookie($name);
    public function getCookies();
    public function getFilesArray();
    public function isConnectionClosed();
    public function getRawInputData();
}