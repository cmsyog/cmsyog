<?php
interface YogSerializer
{
    public function serialize($data);
    public function unserialize($data);
    public function getMethodName();
    public function getContentType();
}