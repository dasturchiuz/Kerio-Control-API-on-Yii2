<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use app\models\LoginForm;
use app\models\ContactForm;


class AdminController extends Controller
{
    public function behaviors(){
        return [
            'access'=>[
                'class'=>\yii\filters\AccessControl::className(),
                'rules'=>[
                    [
                        'allow'=>true,
                        'roles'=>['Admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(){

        $Api=new \KerioControlApi("MyKerioApp", 'Kerio Technologies s.r.o.', '1.0');


        try {
            $session = $Api->login(Yii::$app->params['hostname'], Yii::$app->params['username'], Yii::$app->params['password']);

            $params = array(
                'query' => array(
                    'combining' => 'And',
                    'orderBy' => array(array(
                        'columnName' => 'userName',
                        'direction' => 'Asc'
                    ))
                ),
                'domainId' => 'local' // local database
            );
            $users = $Api->sendRequest('Users.get', $params);

            $quota = array('daily', 'weekly', 'monthly');

            /* Result table */
            print '<table>'
                . '<thead>'
                . '  <th class="control">userName</th>';

            /* Generate dynamic right table */
            foreach ($quota as $interval) {
                printf('<th class="control">%s</th>', $interval);
            }

            print '</thead>'
                . '<tbody>';

            /* Display user rights */
            foreach ($users['list'] as $user) {

                print '<tr>';
                printf('<td>%s</td>', $user['credentials']['userName']);

                foreach ($quota as $interval) {
                    $value = $user['data']['quota'][$interval]['limit']['value'];
                    $units = $user['data']['quota'][$interval]['limit']['units'];

                    printf('<td align="center">%d %s</td>', $value, $units);
                }
                print '</tr>';
            }
            print '</tbody></table>';
        }
        catch (KerioApiException $error) {

            /* Catch possible errors */
           var_dump($error->getMessage());
        }

        /* Logout */
        if (isset($session)) {
            $Api->logout();
        }

    }

    public function actionTraffic(){

        $Api=new \KerioControlApi("MyKerioApp", 'Kerio Technologies s.r.o.', '1.0');


        try {
            $session = $Api->login(Yii::$app->params['hostname'], Yii::$app->params['username'], Yii::$app->params['password']);

            $params = array(
                'query' => array(
                    'combining' => 'And',
                    'orderBy' => array(array(
                        'columnName' => 'userName',
                        'direction' => 'Asc'
                    ))
                ),
                'domainId' => 'local' // local database
            );
            $users = $Api->sendRequest('Users.get', $params);

            $quota = array('daily', 'weekly', 'monthly');

           $data=[];
            /* Display user rights */
            foreach ($users['list'] as $user) {


                $data_item['userName']= $user['credentials']['userName'];
                $name=['daily', 'weekly', 'monthly'];
                $i=0;
                foreach ($quota as $interval) {
                    $value = $user['data']['quota'][$interval]['limit']['value'];
                    $units = $user['data']['quota'][$interval]['limit']['units'];

                    $data_item[$name[$i]]=$value." ".$units;
                    $i++;
                }
                $data[]=$data_item;

            }

        }
        catch (KerioApiException $error) {

            /* Catch possible errors */
           var_dump($error->getMessage());
        }

        /* Logout */
        if (isset($session)) {
            $Api->logout();
        }
        $userName = Yii::$app->request->getQueryParam('userName', '');
        if($userName!=null){
            $data=array_filter($data, function($item)use($userName){
                if (strpos('/^' . strtolower($item['userName']) . '/',strtolower( $userName)) != false) {
                    return true;
                } else {
                    return false;
                }
            });
        }
        $searchModel = [ 'userName' => $userName];
        $searchAttributes = ['userName','daily', 'weekly', 'monthly'];
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => $searchAttributes,
            ],
            'pagination' => ['pageSize' => 20]
        ]);
        return $this->render('traffic', compact( 'dataProvider', 'searchModel'));

    }

    public function actionGroup(){
        $Api=new \KerioControlApi("MyKerioApp", 'Kerio Technologies s.r.o.', '1.0');


        $login = $Api->login(Yii::$app->params['hostname'], Yii::$app->params['username'], Yii::$app->params['password']);
        /* Main application */
        /*        $params = array("query" => array("fields" => array("id","name")), 'domainId' => $domainId);
*/

        try {
            /* Login */
            $params = [
                'query' => [
                    'fields' => [
                        'id',
                        'name',
                        'members'

                    ]
                ],
                'domainId' => 'local' // local database
            ];
            $groups = $Api->sendRequest('UserGroups.get', $params);
            foreach($groups['list'] as $item){
//                echo $item['id']." - ".$item['name']."<br>";
                echo "<pre>";
                var_dump($item);
                die();
            }

        } catch (Exception $error) {
            /* Catch possible errors */
            print $error->getMessage();
        }

        /* Logout */
        if(isset($login)) {
            $Api->logout();
        }
    }

    public function actionDeleteFoydalanuvchi($id){
        $Api=new \KerioControlApi("MyKerioApp", 'Kerio Technologies s.r.o.', '1.0');


        $login = $Api->login(Yii::$app->params['hostname'], Yii::$app->params['username'], Yii::$app->params['password']);
        /* Main application */
        /*        $params = array("query" => array("fields" => array("id","name")), 'domainId' => $domainId);
*/

        try {
            /* Login */
            $params = [

                    'userIds' => [$id],

                'domainId' => 'local' // local database
            ];
            $groups = $Api->sendRequest('Users.remove', $params);
            Yii::$app->session->setFlash('success', "Foydalanuvchi muvoffaqiyatli o'chrildi");
            return $this->redirect(['/admin/users']);
        } catch (Exception $error) {
            /* Catch possible errors */
            print $error->getMessage();
        }

        /* Logout */
        if(isset($login)) {
            $Api->logout();
        }
    }

    public function actionEditFoydalanuvchi($id){
        $model=new \app\models\Keriouser();
        $model->scenario = \app\models\Keriouser::SCENARIO_EDIT;
        $Api=new \KerioControlApi("MyKerioApp", 'Kerio Technologies s.r.o.', '1.0');
        $login = $Api->login(Yii::$app->params['hostname'], Yii::$app->params['username'], Yii::$app->params['password']);
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            try {
                $userGroups=[];
                foreach($model->groups as $item){
                    $userGroups[]=['id'=>$item];
                }
                $userData = array(
                    "data" => array(
                        "rights" => array(
                            "readConfig" => false,
                            "writeConfig" => false,
                            "unlockRule" => false,
                            "dialRasConnection" => false,
                            "connectVpn" => false,
                            "useP2p" => false
                        ),
                        "quota" => array(
                            "daily" => array(
                                "enabled" => true,
                                "type" => "QuotaBoth",
                                "limit" => array(
                                    "value" => 5,
                                    "units" => "MegaBytes"
                                )
                            ),
                            "weekly" => array(
                                "enabled" => $model->quota_weekly!=null ? true : false,
                                "type" => "QuotaBoth",
                                "limit" => array(
                                    "value" => $model->quota_weekly!=null ? $model->quota_weekly : 0,
                                    "units" => "GigaBytes"
                                )
                            ),
                            "monthly" => array(
                                "enabled" => $model->quota_monthly!=null ? true : false,
                                "type" => "QuotaBoth",
                                "limit" => array(
                                    "value" => $model->quota_monthly!=null ? $model->quota_monthly : 0,
                                    "units" => "GigaBytes"
                                )
                            ),
                            "blockTraffic" => false,
                            "notifyUser" => false
                        ),
                        "wwwFilter" => array(
                            "javaApplet" => false,
                            "embedObject" => false,
                            "script" => false,
                            "popup" => false,
                            "referer" => false
                        ),
                        "language" => "detect"
                    ),

                    "fullName" => $model->fullName,
                    "description" =>$model->description,
//                    "email" =>$model->email,
                    "authType" => "Internal",
                    "useTemplate" => false,
                    "adEnabled" => true,
                    "localEnabled" => true,
                    "groups" => $userGroups,
                );


                $params = array(
                    "userIds" => array($id),
                    "details" => $userData,
                    "domainId" => "local"
                );
                $Api->sendRequest("Users.set", $params);

                Yii::$app->session->setFlash('success', "Foydalanuvchi muvoffaqiyatli yangilandi");
                return $this->redirect(['/admin/users']);
            } catch (Exception $error) {
                /* Catch possible errors */
                print $error->getMessage();
            }
            if(isset($login)) {
                $Api->logout();
            }
        }
        $params1 = [
            'query' => [
                'fields' => [
                    'id',
                    'name',

                ]
            ],
            'domainId' => 'local' // local database
        ];
        $groups = $Api->sendRequest('UserGroups.get', $params1);
        /* Main application */

        try {
            /* Login */
            $params = array(
                'query' => array(
                    'combining' => 'And',
                    'conditions' => [
                        [
                            "fieldName"=>"id",
                            "value"=>$id
                        ],
                    ],
                    'orderBy' => array(array(
                        'columnName' => 'userName',
                        'direction' => 'Asc'
                    ))
                ),
                'domainId' => 'local' // local database
            );
            $users = $Api->sendRequest('Users.get', $params);


            foreach($users['list'] as $item){
                $model->credentials_userName=$item['credentials']['userName'];
                $model->fullName=$item['fullName'];
                $model->description=$item['description'];
                $model->email=$item['email'];
                $model->groups=[];
                foreach($item['groups'] as $gr_item){
                    $model->groups[]= $gr_item['id'];
                }


            }
        } catch (Exception $error) {
            /* Catch possible errors */
            print $error->getMessage();
        }


        /* Logout */
        if(isset($login)) {
            $Api->logout();
        }
        $group_dropdown=[];
        foreach($groups['list'] as $item){
            $group_dropdown[]=['id'=>$item['id'], 'name'=>$item['name'], ];
        }
        return $this->render('edit', compact('model', 'group_dropdown'));

    }

    public function actionUsers(){
        $Api=new \KerioControlApi("MyKerioApp", 'Kerio Technologies s.r.o.', '1.0');


        $login = $Api->login(Yii::$app->params['hostname'], Yii::$app->params['username'], Yii::$app->params['password']);
        /* Main application */


        try {
            /* Login */
            $params = array(
                'query' => array(
                    'combining' => 'And',
//                    'conditions' => [
//                        [
//                            "fieldName"=>"userName",
//                            "value"=>"admin"
//                        ],
//                    ],
                    'orderBy' => array(array(
                        'columnName' => 'userName',
                        'direction' => 'Asc'
                    ))
                ),
                'domainId' => 'local' // local database
            );
            $users = $Api->sendRequest('Users.get', $params);

        } catch (Exception $error) {
            /* Catch possible errors */
            print $error->getMessage();
        }

        /* Logout */
        if(isset($login)) {
            $Api->logout();
        }
        $data=[];
        foreach ($users['list'] as $user) {
            // echo "<pre>";
            // var_dump($user);
            $data_item['userName']=$user['credentials']['userName'];
            $data_item['id']=$user['id'];
            $data_item['fullName']=$user['fullName'];
            $data_item['description']=$user['description'];
            $data_item['group']='';
            foreach($user['groups'] as $group){
                $data_item['group'] .= $group['name'].", ";

            }
            $data[]=$data_item;
        }


        $searchModel = [];
        $searchAttributes = ['userName','fullName', 'description', 'group'];

        $fullName = Yii::$app->request->getQueryParam('fullName', '');
        $group = Yii::$app->request->getQueryParam('group', '');
        $userName = Yii::$app->request->getQueryParam('userName', '');
        if($userName!=null){
            $data=array_filter($data, function($item)use($userName){
                if (strpos('/^' . strtolower($item['userName']) . '/',strtolower( $userName)) != false) {
                    return true;
                } else {
                    return false;
                }
            });
        }
        if($fullName!=null){
            $data=array_filter($data, function($item)use($fullName){
                if (strpos('/^' . strtolower($item['fullName']) . '/',strtolower( $fullName)) != false) {
                    return true;
                } else {
                    return false;
                }
            });
        }
        if($group!=null){
            $data=array_filter($data, function($item)use($group){
                if (strpos('/^' . strtolower($item['group']) . '/',strtolower( $group)) != false) {
                    return true;
                } else {
                    return false;
                }
            });
        }

        $searchModel = ['fullName' => $fullName, 'userName' => $userName, 'group'=>$group];
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => $searchAttributes,
            ],
//'sort' =>['attributes' => ['ID', 'Description'],],
            'pagination' => ['pageSize' => 20]
        ]);
        return $this->render('index', compact('users', 'dataProvider', 'searchModel'));
    }


    public function actionCreateUser(){
        $model=new \app\models\Keriouser();
        $params1 = [
            'query' => [
                'fields' => [
                    'id',
                    'name',

                ]
            ],
            'domainId' => 'local' // local database
        ];

        $Api=new \KerioControlApi("MyKerioApp", 'Kerio Technologies s.r.o.', '1.0');
        $login = $Api->login(Yii::$app->params['hostname'], Yii::$app->params['username'], Yii::$app->params['password']);
        $groups = $Api->sendRequest('UserGroups.get', $params1);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $userGroups=[];
            foreach($model->groups as $item){
                $userGroups[]=['id'=>$item];
            }

            try {
                $userData = array(
                    "data" => array(
                        "rights" => array(
                            "readConfig" => false,
                            "writeConfig" => false,
                            "unlockRule" => false,
                            "dialRasConnection" => false,
                            "connectVpn" => false,
                            "useP2p" => false
                        ),
                        "quota" => array(
                            "daily" => array(
                                "enabled" => true,
                                "type" => "QuotaBoth",
                                "limit" => array(
                                    "value" => 5,
                                    "units" => "MegaBytes"
                                )
                            ),
                            "weekly" => array(
                                "enabled" => $model->quota_weekly!=null ? true : false,
                                "type" => "QuotaBoth",
                                "limit" => array(
                                    "value" => $model->quota_weekly!=null ? $model->quota_weekly : 0,
                                    "units" => "GigaBytes"
                                )
                            ),
                            "monthly" => array(
                                "enabled" => $model->quota_monthly!=null ? true : false,
                                "type" => "QuotaBoth",
                                "limit" => array(
                                    "value" => $model->quota_monthly!=null ? $model->quota_monthly : 0,
                                    "units" => "GigaBytes"
                                )
                            ),
                            "blockTraffic" => false,
                            "notifyUser" => false
                        ),
                        "wwwFilter" => array(
                            "javaApplet" => false,
                            "embedObject" => false,
                            "script" => false,
                            "popup" => false,
                            "referer" => false
                        ),
                        "language" => "detect"
                    ),
                    "credentials" => array(
                        "userName" => $model->credentials_userName,
                        "password" => $model->credentials_password,
                        "passwordChanged" => true
                    ),
                    "fullName" => $model->fullName,
                    "description" =>$model->description,
                    "email" =>$model->email,
                    "authType" => "Internal",
                    "useTemplate" => true,
                    "adEnabled" => true,
                    "localEnabled" => true,
                    "groups" => $userGroups,
                );

                $params = array(
                    "users" => array($userData),
                    "domainId" => "local"
                );

                /* Create user */
                $result = $Api->sendRequest("Users.create", $params);
                $userId = $result['result'][0]['id'];

                /* Update user */
                $userData["fullName"] =$model->fullName;
                $params = array(
                    "userIds" => array($userId),
                    "details" => $userData,
                    "domainId" => "local"
                );
                $Api->sendRequest("Users.set", $params);
                Yii::$app->session->setFlash('success', $model->credentials_userName." ".$model->fullName." - Foydalanuvchi muvoffaqiyatli qo'shildi");
                return $this->redirect(['/admin/users']);
            } catch (Exception $error) {
                /* Catch possible errors */
                print $error->getMessage();
            }

            /* Logout */
            if(isset($login)) {
                $Api->logout();
            }

        }
        $group_dropdown=[];
        foreach($groups['list'] as $item){
            $group_dropdown[]=['id'=>$item['id'], 'name'=>$item['name'], ];
        }

        return $this->render('create', compact('model', 'group_dropdown'));
    }


    public function actionEmployee(){

        $dataProvider=new ActiveDataProvider([
            'query'=>\app\models\User::find(),
            'pagination'=>[
              'pageSize'=>20,
            ],
        ]);

        return $this->render("employee", compact('dataProvider'));
    }

    public function actionCreateEmployee(){
        $model=new \app\models\User();
        $connection= \Yii::$app->db;
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $transaction=$connection->beginTransaction();
            try{
                $model->password_hash=Yii::$app->getSecurity()->generatePasswordHash($model->password_hash);
                $model->save(false);
                $auth=Yii::$app->authManager;
                $rol=$auth->getRole($model->role);
                $auth->assign($rol, $model->id);
                $transaction->commit();
                Yii::$app->session->setFlash('success', $model->fio." - Foydalanuvchi muvoffaqiyatli qo'shildi");
                return $this->redirect(['/admin/employee']);
            }catch(Exceptin $e){
                $transaction->rollback();
            }
        }
        return $this->render("createemployee", compact("model"));
    }
}