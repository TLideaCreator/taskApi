<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'guest', 'namespace' => 'App\Http\Controllers'], function ($api) {
        $api->post('user/login', 'User\AuthCtrl@login');
    });
    $api->group(['prefix' => 'projects', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Projects'], function ($api) {
        $api->get('/', 'ProjectCtrl@projectList');
        $api->get('/latest/time', 'ProjectCtrl@getLastProjectList');
        $api->post('/', 'ProjectCtrl@createProject');
        $api->get('/{projectId}', 'ProjectCtrl@projectDetail');
    });

    $api->group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers\User'], function ($api) {
        $api->get('/users', 'UserCtrl@getUserList');
        $api->post('/users', 'UserCtrl@createUser');
        $api->get('/users/{userId}', 'UserCtrl@getUserDetail');
        $api->patch('/users/{userId}', 'UserCtrl@updateUser');
        $api->delete('/users/{userId}', 'UserCtrl@deleteUser');
    });

    $api->group(['prefix' => 'projects', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Sprints'], function ($api) {
        $api->get('/{projectId}/sprints', 'SprintCtrl@getProjectSprintList');
        $api->post('/{projectId}/sprints', 'SprintCtrl@createSprints');
        $api->post('/sprints/{sprintId}/implementation', 'SprintCtrl@makeSprintsActive');
        $api->patch('/sprints/{sprintId}/finish', 'SprintCtrl@makeSprintsFinish');
    });

    $api->group(['prefix' => 'projects/sprints', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Tasks'], function ($api) {
        $api->get('/tasks/{taskId}', 'TaskCtrl@getTaskDetail');
        $api->patch('/tasks/{taskId}', 'TaskCtrl@updateTask');
        $api->post('/{sprintId}/tasks', 'TaskCtrl@createTask');
        $api->patch('/tasks/{taskId}/movement/sprints/{sprintId}', 'TaskCtrl@moveTaskToSprint');

    });

    $api->group(['prefix' => 'system', 'middleware' => ['auth', 'sys_auth'], 'namespace' => 'App\Http\Controllers\System'], function ($api) {
        $api->get('statics', 'SystemCtrl@getSystemStatics');
    });
    $api->group(['prefix' => 'system', 'middleware' => ['auth'], 'namespace' => 'App\Http\Controllers\SystemTemp'], function ($api) {
        $api->get('template', 'SystemTempCtrl@getSystemTemplateList');
    });
    $api->group(['prefix' => 'system/users', 'middleware' => ['auth', 'sys_auth'], 'namespace' => 'App\Http\Controllers\System'], function ($api) {
        $api->get('', 'SystemUserCtrl@getSystemUser');
        $api->post('', 'SystemUserCtrl@createSystemUser');
        $api->get('{userId}', 'SystemUserCtrl@getSystemUserDetail');
        $api->patch('{userId}', 'SystemUserCtrl@updateSystemUser');
        $api->delete('{userId}', 'SystemUserCtrl@deleteSystemUser');
    });

    $api->group(['prefix' => 'system/template', 'middleware' => ['auth', 'sys_auth'], 'namespace' => 'App\Http\Controllers\SystemTemp'], function ($api) {
        $api->post('', 'SystemTempCtrl@createSystemTemplate');
        $api->get('{tempId}', 'SystemTempCtrl@getSystemTemplateDetail');
        $api->patch('{tempId}', 'SystemTempCtrl@updateSystemTemplate');
        $api->delete('{tempId}', 'SystemTempCtrl@deleteSystemTemplate');

        $api->get('{tempId}/roles', 'SystemTempRoleCtrl@getSystemTemplateRole');
        $api->post('{tempId}/roles', 'SystemTempRoleCtrl@createSystemTemplateRole');
        $api->patch('{tempId}/roles/{roleId}', 'SystemTempRoleCtrl@updateSystemTemplateRole');
        $api->delete('{tempId}/roles/{roleId}', 'SystemTempRoleCtrl@deleteSystemTemplateRole');

        $api->get('{tempId}/status', 'SystemTempStatusCtrl@getSystemTemplateStatus');
        $api->post('{tempId}/status', 'SystemTempStatusCtrl@createSystemTemplateStatus');
        $api->patch('{tempId}/status/sequence', 'SystemTempStatusCtrl@updateTemplateSequence');
        $api->patch('{tempId}/status/{statusId}', 'SystemTempStatusCtrl@updateSystemTemplateStatus');
        $api->delete('{tempId}/status/{statusId}', 'SystemTempStatusCtrl@deleteSystemTemplateStatus');

        $api->get('{tempId}/types', 'SystemTempTypeCtrl@getSystemTemplateTypes');
        $api->post('{tempId}/types', 'SystemTempTypeCtrl@createSystemTemplateTypes');
        $api->patch('{tempId}/types/{typeId}', 'SystemTempTypeCtrl@updateSystemTemplateTypes');
        $api->delete('{tempId}/types/{typeId}', 'SystemTempTypeCtrl@deleteSystemTemplateTypes');

        $api->get('{tempId}/priorities', 'SystemTempPriorityCtrl@getSystemTemplatePriority');
        $api->post('{tempId}/priorities', 'SystemTempPriorityCtrl@createSystemTemplatePriority');
        $api->patch('{tempId}/priorities/sequence', 'SystemTempPriorityCtrl@updatePrioritySequence');
        $api->patch('{tempId}/priorities/{priorityId}', 'SystemTempPriorityCtrl@updateSystemTemplatePriority');
        $api->delete('{tempId}/priorities/{priorityId}', 'SystemTempPriorityCtrl@deleteSystemTemplatePriority');
    });

    $api->group(['prefix' => 'projects/{projectId}', 'middleware' => ['auth', 'project_auth'], 'namespace' => 'App\Http\Controllers\Projects'], function ($api) {
        $api->patch('', 'ProjectCtrl@updateProject');
        $api->delete('', 'ProjectCtrl@removeProject');

        $api->get('/members', 'ProjectMemberCtrl@getProjectMemberList');
        $api->post('/members/{memberId}/roles/{roleId}', 'ProjectMemberCtrl@addMemberToProject');
        $api->delete('/members/{memberId}', 'ProjectMemberCtrl@delProjectMembers');

        $api->get('/settings/roles', 'ProjectRoleCtrl@getProjectRole');
        $api->post('/settings/roles', 'ProjectRoleCtrl@createProjectRole');
        $api->patch('/settings/roles/{roleId}', 'ProjectRoleCtrl@updateProjectRole');
        $api->delete('/settings/roles/{roleId}', 'ProjectRoleCtrl@deleteProjectRole');

        $api->get('/settings/status', 'ProjectStatusCtrl@getProjectStatus');
        $api->post('/settings/status', 'ProjectStatusCtrl@createProjectStatus');
        $api->patch('/settings/status/sequence', 'ProjectStatusCtrl@updateProjectStatusSequence');
        $api->patch('/settings/status/{statusId}', 'ProjectStatusCtrl@updateProjectStatus');
        $api->delete('/settings/status/{statusId}', 'ProjectStatusCtrl@deleteProjectStatus');

        $api->get('/settings/types', 'ProjectTypeCtrl@getProjectTypes');
        $api->post('/settings/types', 'ProjectTypeCtrl@createProjectTypes');
        $api->patch('/settings/types/{statusId}', 'ProjectTypeCtrl@updateProjectTypes');
        $api->delete('/settings/types/{statusId}', 'ProjectTypeCtrl@deleteProjectTypes');

        $api->get('/settings/priorities', 'ProjectPrioritiesCtrl@getProjectPriority');
        $api->post('/settings/priorities', 'ProjectPrioritiesCtrl@createProjectPriority');
        $api->patch('/settings/priorities/sequence', 'ProjectPrioritiesCtrl@updateProjectPrioritySequence');
        $api->patch('/settings/priorities/{priorityId}', 'ProjectPrioritiesCtrl@updateProjectPriority');
        $api->delete('/settings/priorities/{priorityId}', 'ProjectPrioritiesCtrl@deleteProjectPriority');

    });

});
