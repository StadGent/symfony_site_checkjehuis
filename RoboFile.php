<?php

use DigipolisGent\Robo\Symfony\RoboFileBase;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

class RoboFile extends RoboFileBase
{
    protected function postSymlinkTask($worker, AbstractAuth $auth, $remote)
    {
        $currentProjectRoot = $remote['currentdir'] . '/..';
        $collection = $this->collectionBuilder();
        $parent = parent::postSymlinkTask($worker, $auth, $remote);
        if ($parent) {
            $collection->addTask($parent);
        }
        $collection->taskSsh($worker, $auth)
            ->remoteDirectory($currentProjectRoot, true)
            ->exec('composer symfony-scripts');
        return $collection;
    }
}
