<?php


class SqlCall
{
    static function _installSql() {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product ADD threelib TEXT NULL";
        $returnSql = Db::getInstance()->execute($sqlInstall);
        return $returnSql;
    }
    static function _unInstallSql() {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product DROP threelib";
        $returnSql = Db::getInstance()->execute($sqlInstall);

        return true;
    }

}