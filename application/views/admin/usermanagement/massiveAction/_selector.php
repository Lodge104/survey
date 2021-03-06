<!-- Rendering massive action widget -->
<?php

$aActionsArray = array(
    'pk'          => 'selectedUser',
    'gridid'      => 'usermanagement--identity-gridPanel',
    'dropupId'    => 'usermanagement--actions',
    'dropUpText'  => gT('Selected users(s)...'),

    'aActions'    => array(
        
        // Delete
        array(
            'type'          => 'action',
            'action'        => 'delete',
            'url'           =>  App()->createUrl('/admin/usermanagement/sa/deleteMultiple'),
            'iconClasses'   => 'text-danger fa fa-trash',
            'text'          =>  gT('Delete'),
            'grid-reload'   => 'yes',
            'actionType'    => 'modal',
            'modalType'     => 'yes-no',
            'keepopen'      => 'yes',
            'showSelected'  => 'yes',
            'selectedUrl'   => App()->createUrl('/admin/usermanagement/sa/renderSelectedItems/'),
            'sModalTitle'   => gT('Delete user'),
            'htmlModalBody' => gT('Are you sure you want to delete the selected user?'),
        ),
        // ResendLoginData
        array(
            'type'          => 'action',
            'action'        => 'resendlogindata',
            'url'           =>  App()->createUrl('/admin/usermanagement/sa/batchSendAndResetLoginData'),
            'iconClasses'   => 'text-success fa fa-refresh',
            'text'          =>  gT('Resend login data'),
            'grid-reload'   => 'yes',
            'actionType'    => 'modal',
            'modalType'     => 'yes-no',
            'keepopen'      => 'yes',
            'showSelected'  => 'yes',
            'selectedUrl'   => App()->createUrl('/admin/usermanagement/sa/renderSelectedItems/'),
            'sModalTitle'   => gT('Resend login data user'),
            'htmlModalBody' => gT('Are you sure you want to reset and resend selected users login data?'),
        ),
        // Mass Edit
        array(
            'type'              => 'action',
            'action'            => 'batchPermissions',
            'url'               => App()->createUrl('/admin/usermanagement/sa/batchPermissions'),
            'iconClasses'       => 'fa fa-unlock',
            'text'              => gT('Edit permissions'),
            'grid-reload'       => 'yes',
            //modal
            'actionType'        => 'modal',
            'modalType'         => 'yes-no-lg',
            'keepopen'          => 'yes',
            'showSelected'      => 'yes',
            'selectedUrl'       => App()->createUrl('/admin/usermanagement/sa/renderSelectedItems/'),
            'sModalTitle'       => gT('Edit permissions'),
            'htmlFooterButtons' => [],
            'htmlModalBody'     => App()->getController()->renderPartial('/admin/usermanagement/massiveAction/_updatepermissions', [], true)
        ),
        
    )
);

if(Permission::model()->hasGlobalPermission('users', 'update')) {
    // Mass Edit -> roles only for superadmins
    $aActionsArray['aActions'][] = array(
        'type'          => 'action',
        'action'        => 'batchaddtogroup',
        'url'           => App()->createUrl('/admin/usermanagement/sa/batchAddGroup'),
        'iconClasses'   => 'fa fa-users',
        'text'          => gT('Add to usergroup'),
        'grid-reload'   => 'yes',
        //modal
        'actionType'    => 'modal',
        'modalType'     => 'yes-no-lg',
        'keepopen'      => 'yes',
        'showSelected'  => 'yes',
        'selectedUrl'   => App()->createUrl('/admin/usermanagement/sa/renderSelectedItems/'),
        'sModalTitle'   => gT('Add to usergroup'),
        'htmlModalBody' => App()->getController()->renderPartial('/admin/usermanagement/massiveAction/_addtousergroup', [], true)
    );
}

if(Permission::model()->hasGlobalPermission('superadmin','read')) {
    // Mass Edit -> roles only for superadmins
    $aActionsArray['aActions'][] = array(
        'type'              => 'action',
        'action'            => 'batchaddrole',
        'url'               => App()->createUrl('/admin/usermanagement/sa/batchApplyRoles'),
        'iconClasses'       => 'fa fa-address-card-o',
        'text'              => gT('Add role'),
        'grid-reload'       => 'yes',
        //modal
        'actionType'        => 'modal',
        'modalType'         => 'yes-no-lg',
        'keepopen'          => 'yes',
        'showSelected'      => 'yes',
        'selectedUrl'       => App()->createUrl('/admin/usermanagement/sa/renderSelectedItems/'),
        'sModalTitle'       => gT('Add role'),
        'htmlFooterButtons' => [],
        'htmlModalBody'     => App()->getController()->renderPartial('/admin/usermanagement/massiveAction/_updaterole', [], true)
    );
}


$this->widget('ext.admin.grid.MassiveActionsWidget.MassiveActionsWidget', $aActionsArray);