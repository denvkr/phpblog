<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpBlog;

/**
 * Description of getadminuser
 *
 * @author Нехай
 */
class getadminusercommand {
    protected function configure()
    {
    $this
        // the name of the command (the part after "app/console")
        ->setName('app:create-users')

        // the short description shown while running "php app/console list"
        ->setDescription('Creates new users.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp("This command allows you to create users...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return;
    }
}
