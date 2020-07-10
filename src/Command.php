<?php


namespace bolongo\phppdfcrop;

use \mikehaertl\shellcommand\Command as BaseCommand;

class Command extends BaseCommand {

    /**
     * Adds the list of args to the command with the required prefix
     * @param array $args
     */
    public function addArgs($args) {
        if(isset($args['input'])) {
            $this->addArg((string) $args['input']);
            unset($args['input']);
        }

        foreach ($args as $key => $value) {
            if(isset($value)) {
                $this->addArg("--$key", $value);
            } else {
                $this->addArg("--$key");
            }
        }
    }
}