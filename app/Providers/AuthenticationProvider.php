<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hiject\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Hiject\Core\Security\AuthenticationManager;
use Hiject\Core\Security\AccessMap;
use Hiject\Core\Security\Authorization;
use Hiject\Core\Security\Role;
use Hiject\Auth\RememberMeAuth;
use Hiject\Auth\DatabaseAuth;
use Hiject\Auth\LdapAuth;
use Hiject\Auth\TotpAuth;
use Hiject\Auth\ReverseProxyAuth;

/**
 * Authentication Provider
 */
class AuthenticationProvider implements ServiceProviderInterface
{
    /**
     * Register providers
     *
     * @access public
     * @param  \Pimple\Container $container
     * @return \Pimple\Container
     */
    public function register(Container $container)
    {
        $container['authenticationManager'] = new AuthenticationManager($container);
        $container['authenticationManager']->register(new TotpAuth($container));
        $container['authenticationManager']->register(new RememberMeAuth($container));
        $container['authenticationManager']->register(new DatabaseAuth($container));

        if (REVERSE_PROXY_AUTH) {
            $container['authenticationManager']->register(new ReverseProxyAuth($container));
        }

        if (LDAP_AUTH) {
            $container['authenticationManager']->register(new LdapAuth($container));
        }

        $container['projectAccessMap'] = $this->getProjectAccessMap();
        $container['applicationAccessMap'] = $this->getApplicationAccessMap();
        $container['apiAccessMap'] = $this->getApiAccessMap();
        $container['apiProjectAccessMap'] = $this->getApiProjectAccessMap();

        $container['projectAuthorization'] = new Authorization($container['projectAccessMap']);
        $container['applicationAuthorization'] = new Authorization($container['applicationAccessMap']);
        $container['apiAuthorization'] = new Authorization($container['apiAccessMap']);
        $container['apiProjectAuthorization'] = new Authorization($container['apiProjectAccessMap']);

        return $container;
    }

    /**
     * Get ACL for projects
     *
     * @access public
     * @return AccessMap
     */
    public function getProjectAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::PROJECT_VIEWER);
        $acl->setRoleHierarchy(Role::PROJECT_MANAGER, array(Role::PROJECT_MEMBER, Role::PROJECT_VIEWER));
        $acl->setRoleHierarchy(Role::PROJECT_MEMBER, array(Role::PROJECT_VIEWER));

        $acl->add('ActionController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectActionDuplicationController', '*', Role::PROJECT_MANAGER);
        $acl->add('ActionCreationController', '*', Role::PROJECT_MANAGER);
        $acl->add('AnalyticController', '*', Role::PROJECT_MANAGER);
        $acl->add('BoardAjaxController', 'save', Role::PROJECT_MEMBER);
        $acl->add('BoardPopoverController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskPopoverController', '*', Role::PROJECT_MEMBER);
        $acl->add('CalendarController', 'save', Role::PROJECT_MEMBER);
        $acl->add('CategoryController', '*', Role::PROJECT_MANAGER);
        $acl->add('ColumnController', '*', Role::PROJECT_MANAGER);
        $acl->add('CommentController', '*', Role::PROJECT_MEMBER);
        $acl->add('CustomFilterController', '*', Role::PROJECT_MEMBER);
        $acl->add('ExportController', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskFileController', array('screenshot', 'create', 'save', 'remove', 'confirm'), Role::PROJECT_MEMBER);
        $acl->add('TaskGanttController', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskGanttCreationController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectViewController', array('share', 'updateSharing', 'integrations', 'updateIntegrations', 'notifications', 'updateNotifications', 'duplicate', 'doDuplication'), Role::PROJECT_MANAGER);
        $acl->add('ProjectPermissionController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectEditController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectFileController', '*', Role::PROJECT_MEMBER);
        $acl->add('ProjectUserOverviewController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectStatusController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectTagController', '*', Role::PROJECT_MANAGER);
        $acl->add('SubtaskController', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskRestrictionController', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskStatusController', '*', Role::PROJECT_MEMBER);
        $acl->add('SwimlaneController', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskSuppressionController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskCreationController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskBulkController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskDuplicationController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskRecurrenceController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskImportController', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskInternalLinkController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskExternalLinkController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskModificationController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskStatusController', '*', Role::PROJECT_MEMBER);
        $acl->add('UserAjaxController', array('mention'), Role::PROJECT_MEMBER);

        return $acl;
    }

    /**
     * Get ACL for the application
     *
     * @access public
     * @return AccessMap
     */
    public function getApplicationAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::APP_USER);
        $acl->setRoleHierarchy(Role::APP_ADMIN, array(Role::APP_MANAGER, Role::APP_USER, Role::APP_PUBLIC));
        $acl->setRoleHierarchy(Role::APP_MANAGER, array(Role::APP_USER, Role::APP_PUBLIC));
        $acl->setRoleHierarchy(Role::APP_USER, array(Role::APP_PUBLIC));

        $acl->add('AuthController', array('login', 'check'), Role::APP_PUBLIC);
        $acl->add('CaptchaController', '*', Role::APP_PUBLIC);
        $acl->add('PasswordResetController', '*', Role::APP_PUBLIC);
        $acl->add('TaskViewController', 'readonly', Role::APP_PUBLIC);
        $acl->add('BoardViewController', 'readonly', Role::APP_PUBLIC);
        $acl->add('ICalendarController', '*', Role::APP_PUBLIC);
        $acl->add('FeedController', '*', Role::APP_PUBLIC);
        $acl->add('AvatarFileController', 'show', Role::APP_PUBLIC);

        $acl->add('ConfigController', '*', Role::APP_ADMIN);
        $acl->add('TagController', '*', Role::APP_ADMIN);
        $acl->add('PluginController', '*', Role::APP_ADMIN);
        $acl->add('CurrencyController', '*', Role::APP_ADMIN);
        $acl->add('ProjectGanttController', '*', Role::APP_MANAGER);
        $acl->add('GroupListController', '*', Role::APP_ADMIN);
        $acl->add('GroupCreationController', '*', Role::APP_ADMIN);
        $acl->add('GroupModificationController', '*', Role::APP_ADMIN);
        $acl->add('LinkController', '*', Role::APP_ADMIN);
        $acl->add('ProjectCreationController', 'create', Role::APP_MANAGER);
        $acl->add('ProjectUserOverviewController', '*', Role::APP_MANAGER);
        $acl->add('TwoFactorController', 'disable', Role::APP_ADMIN);
        $acl->add('UserImportController', '*', Role::APP_ADMIN);
        $acl->add('UserCreationController', '*', Role::APP_ADMIN);
        $acl->add('UserListController', '*', Role::APP_ADMIN);
        $acl->add('UserStatusController', '*', Role::APP_ADMIN);
        $acl->add('UserCredentialController', array('changeAuthentication', 'saveAuthentication', 'unlock'), Role::APP_ADMIN);

        return $acl;
    }

    /**
     * Get ACL for the API
     *
     * @access public
     * @return AccessMap
     */
    public function getApiAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::APP_USER);
        $acl->setRoleHierarchy(Role::APP_ADMIN, array(Role::APP_MANAGER, Role::APP_USER, Role::APP_PUBLIC));
        $acl->setRoleHierarchy(Role::APP_MANAGER, array(Role::APP_USER, Role::APP_PUBLIC));

        $acl->add('UserProcedure', '*', Role::APP_ADMIN);
        $acl->add('GroupMemberProcedure', '*', Role::APP_ADMIN);
        $acl->add('GroupProcedure', '*', Role::APP_ADMIN);
        $acl->add('LinkProcedure', '*', Role::APP_ADMIN);
        $acl->add('TaskProcedure', array('getOverdueTasks'), Role::APP_ADMIN);
        $acl->add('ProjectProcedure', array('getAllProjects'), Role::APP_ADMIN);
        $acl->add('ProjectProcedure', array('createProject'), Role::APP_MANAGER);

        return $acl;
    }

    /**
     * Get ACL for the API
     *
     * @access public
     * @return AccessMap
     */
    public function getApiProjectAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::PROJECT_VIEWER);
        $acl->setRoleHierarchy(Role::PROJECT_MANAGER, array(Role::PROJECT_MEMBER, Role::PROJECT_VIEWER));
        $acl->setRoleHierarchy(Role::PROJECT_MEMBER, array(Role::PROJECT_VIEWER));

        $acl->add('ActionProcedure', array('removeAction', 'getActions', 'createAction'), Role::PROJECT_MANAGER);
        $acl->add('CategoryProcedure', '*', Role::PROJECT_MANAGER);
        $acl->add('ColumnProcedure', '*', Role::PROJECT_MANAGER);
        $acl->add('CommentProcedure', array('removeComment', 'createComment', 'updateComment'), Role::PROJECT_MEMBER);
        $acl->add('ProjectPermissionProcedure', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectProcedure', array('updateProject', 'removeProject', 'enableProject', 'disableProject', 'enableProjectPublicAccess', 'disableProjectPublicAccess'), Role::PROJECT_MANAGER);
        $acl->add('SubtaskProcedure', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskTimeTrackingProcedure', '*', Role::PROJECT_MEMBER);
        $acl->add('SwimlaneProcedure', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectFileProcedure', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskFileProcedure', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskLinkProcedure', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskExternalLinkProcedure', array('createExternalTaskLink', 'updateExternalTaskLink', 'removeExternalTaskLink'), Role::PROJECT_MEMBER);
        $acl->add('TaskProcedure', '*', Role::PROJECT_MEMBER);

        return $acl;
    }
}