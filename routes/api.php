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
    $api->group(['prefix'=>'projects','middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Projects'], function ($api) {
        $api->get('/', 'ProjectCtrl@projectList');
        $api->get('/latest/time', 'ProjectCtrl@getLastProjectList');
        $api->post('/', 'ProjectCtrl@createProject');
        $api->get('/{projectId}', 'ProjectCtrl@projectDetail');
        $api->patch('/{projectId}', 'ProjectCtrl@updateProject');
        $api->delete('/{projectId}', 'ProjectCtrl@removeProject');

        $api->get('/{projectId}/members', 'ProjectMemberCtrl@getProjectMemberList');
        $api->post('/{projectId}/members/{memberId}/roles/{roleId}', 'ProjectMemberCtrl@addMemberToProject');
        $api->delete('/{projectId}/members/{memberId}', 'ProjectMemberCtrl@delProjectMembers');
    });

    $api->group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers\User'], function ($api) {
        $api->get('/users', 'UserCtrl@getUserList');
        $api->post('/users', 'UserCtrl@createUser');
        $api->get('/users/{userId}', 'UserCtrl@getUserDetail');
        $api->patch('/users/{userId}', 'UserCtrl@updateUser');
        $api->delete('/users/{userId}', 'UserCtrl@deleteUser');
    });

    $api->group(['prefix'=>'system','middleware'=>'auth', 'namespace'=> 'App\Http\Controllers\System'],function ($api){
        $api->get('template', 'SystemCtrl@getSystemTemplate');
    });

    $api->group(['prefix'=>'projects','middleware'=>'auth', 'namespace'=> 'App\Http\Controllers\Sprints'], function ($api){
        $api->get('/{projectId}/sprints', 'SprintCtrl@getProjectSprintList');
        $api->post('/{projectId}/sprints', 'SprintCtrl@createSprints');
        $api->post('/sprints/{sprintId}/implementation', 'SprintCtrl@makeSprintsActive');
    });

    $api->group(['prefix'=>'projects/sprints', 'middleware'=>'auth', 'namespace'=>'App\Http\Controllers\Tasks'], function ($api){
        $api->get('/tasks/{taskId}', 'TaskCtrl@getTaskDetail');
        $api->patch('/tasks/{taskId}', 'TaskCtrl@updateTask');
        $api->post('/{sprintId}/tasks', 'TaskCtrl@createTask');
        $api->patch('/tasks/{taskId}/movement/sprints/{sprintId}', 'TaskCtrl@moveTaskToSprint');

    });
});
