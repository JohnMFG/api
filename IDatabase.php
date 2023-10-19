<?php
interface IDatabase
{
    public function query($sql, $params, $fetchMode);
    public function getLastInsertId();
}