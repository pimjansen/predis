<?php

namespace Predis\Commands;

class InfoV24x extends Command {
    public function canBeHashed()  { return false; }
    public function getId() { return 'INFO'; }
    public function parseResponse($data) {
        $info      = array();
        $current   = null;
        $infoLines = explode("\r\n", $data, -1);
        foreach ($infoLines as $row) {
            if ($row === '') {
                continue;
            }
            if (preg_match('/^# (\w+)$/', $row, $matches)) {
                $info[$matches[1]] = array();
                $current = &$info[$matches[1]];
                continue;
            }
            list($k, $v) = explode(':', $row);
            if (!preg_match('/^db\d+$/', $k)) {
                $current[$k] = $v;
            }
            else {
                $db = array();
                foreach (explode(',', $v) as $dbvar) {
                    list($dbvk, $dbvv) = explode('=', $dbvar);
                    $db[trim($dbvk)] = $dbvv;
                }
                $current[$k] = $db;
            }
        }
        return $info;
    }
}