<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function login(Request $request) {
        if ($request->isMethod('post')) {
            $input = $request->only('login_id', 'password');
            $validator = \Validator::make($input, ['login_id' => 'required', 'password' => 'required']);
            if ($validator->passes()) {
                $field1 = 'login_id';
                $field2 = $request['login_id'];
                $field3 = 'phone';
                //Find Login type
//                $login_type = \helpers::findLoginType($request['login_id']);
//                if ($login_type == 'email') {
//                    $field1 = 'email';
//                }
                if ($request['flag'] == 'user') {
                    //get data from user table
                    //$result = MencoUser::where($field1, $field2)->first();
                    $result = MencoUser::where($field1, $field2)->orWhere($field3, $field2)->first();
              
                if ($result) {
  
                    if (\Hash::check($request['password'], $result->password)) {
                        //Authenticate and generate the token
                        $auth = new MencoAuthentication();
                        $result_data['token'] = $auth->generateToken($result['login_id']);
                       $result_data['status'] = $result['status'];
         
                        $response = ['code' => 200, 'message' => 'Login Successfully', 'data' => $result_data];
                    } else {
                        $response = ['code' => 401, 'message' => 'Password is invalid', 'data' => ''];
                    }
                } else {
                    $response = ['code' => 204, 'message' => 'User not found', 'data' => ''];
                }
            } else {
                $response = ['code' => 401, 'message' => $validator->errors()];
            }
        } else {
            $response = ['code' => 204, 'message' => 'No post data.'];
        }
        return response($response)->header('Content-Type', 'application/json');
    }
}
public function createUser(Request $req) {

    if ($req->isMethod('post')) {

        $auth = new MencoAuthentication;
        $login_id = $req['login_id'];
        $token = $req['token'];

        $validator = Validator::make($req->all(), [
                    'login_id' => 'required',
                    'token' => 'required',
                    'email' => 'email|unique:menco_users,email',
                    'phone' => 'numeric|unique:menco_users,phone',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['errors'] = $errors;
            return response($response)->header('Content-Type', 'application/json');
        }
        if (!$auth->ValidateToken($login_id, $token)) {

            $response['messange'] = 'Invalid credentials';
            $response['code'] = 417;
            return response($response)->header('Content-Type', 'application/json');
        }

        if (isset($req['role_id'])) {

            //Common inputs for all the roles except student
            $name = $req['name'];
            $email = ($req['email'] ? $req['email'] : null);
            $phone = ($req['phone'] ? $req['phone'] : null);
            $role_id = $req['role_id'];

            //Other inputs for respective roles
            $teacher_code = $req['teacher_code'];
            $subject_id = $req['subject_id'];
            $school_id = $req['school_id'];
            $nextyear = date("Y") + 1;
            $currentyear = date("Y");
            $currentSession = $currentyear . "-" . $nextyear;

            $validator = Validator::make($req->all(), [
                        'email' => 'email|unique:menco_users,email',
                        'phone' => 'numeric|unique:menco_users,phone',
                        'name' => 'alpha|required',
                        'role_id' => 'numeric|required',
            ]);

            //Validate the inputs
            if ($validator->fails()) {

                $errors = $validator->errors();

                // Bake the response with errors
                $response['error'] = $errors;

                // Serve the response
                return response($response)->header('Content-Type', 'application/json');
            }
            $role = MencoRole::where('id', $role_id)->first();

            if (!$role) {
                $response['code'] = 417;
                $response['error'] = 'Role is not available. Please add it.';
                return response($response)->header('Content-Type', 'application/json');
            }

            //Creating the user with common inputs || just make sure his is not student
            if ($role->id != 8) {

                //Prepare login_id and password
                $loginid = MencoUser::withTrashed()->orderby('id', 'desc')->first();
                if ($loginid->login_id) {
                    $loginid = substr($loginid->login_id, 4);
                    $loginid = (int) ($loginid + 1);
                    $newLogin_id = 'user' . (string) $loginid; //Generating new login_id
                    $newPassword = \Hash::make('menco123');
                }

                //Create new user
                $newUser = new MencoUser;
                $newUser->login_id = $newLogin_id;
                $newUser->password = $newPassword;
                $newUser->email = $email;
                $newUser->phone = $phone;
                $newUser->save();
                $newUser->id; //new user id
                //Generating profile for the new user
                $newUserProfile = new MencoUserProfile;
                $newUserProfile->user_id = $newUser->id;
                $newUserProfile->name = $name;
                $newUserProfile->address_id = 1;
                $newUserProfile->save();
            }

            switch ($role->id) {
                case '2': {
                        //Menco-Expert
                        //Store the role in menco_user_role table
                        $newUserRole = new MencoUserRole;
                        $newUserRole->user_id = $newUser->id; //store the new user id
                        $newUserRole->role_id = 9;
                        $newUserRole->save();
                        break;
                    }
                case '3': {
                        //School-admin
                        //Store the role in menco_user_role table
                        $newUserRole = new MencoUserRole;
                        $newUserRole->user_id = $newUser->id; //store the new user id
                        $newUserRole->role_id = 9;
                        $newUserRole->save();

                        //Now store this user in school_admins reference table
                        $newSchoolAdmin = new MencoSchoolAdmin;
                        $newSchoolAdmin->user_id = $newUser->id;
                        $newSchoolAdmin->school_id = $school_id;
                        $newSchoolAdmin->save();

                        //Preparing the the response
                        $response['status'] = 'success';
                        $response['message'] = 'School admin has been created successfully!';
                        $response['code'] = 200;
                        $response['data'] = array('user_id' => $newUser->id);

                        //Sending the response
                        return response($response)->header('Content-Type', 'application/json');
                        break;
                    }
                case '4': {
                        //Principal

                        break;
                    }
                case '5': {
                        //HEAD_TEACHER
                        break;
                    }
                case '6': {
                        //CLASS_TEACHER
                        break;
                    }
                case '7': {
                        //TEACHER

                        $validator = Validator::make($req->all(), [
                                    'teacher_code' => 'required',
                                    'subject_id' => 'required',
                        ]);

                        //Validate the inputs
                        if ($validator->fails()) {

                            $errors = $validator->errors();

                            // Bake the response with errors
                            $response['error'] = $errors;

                            // Serve the response
                            return response($response)->header('Content-Type', 'application/json');
                        }

                        //Store the role in menco_user_role table
                        $newUserRole = new MencoUserRole;
                        $newUserRole->user_id = $newUser->id; //store the new user id
                        $newUserRole->role_id = 9;
                        $newUserRole->save();

                        //Now, store this teacher in menco_school_teachers
                        $newSchoolTeacher = new MencoSchoolTeacher;
                        $newSchoolTeacher->user_id = $newUser->id;
                        $newSchoolTeacher->code = $teacher_code;
                        $newSchoolTeacher->subject_id = $subject_id;
                        $newSchoolTeacher->school_id = $school_id;
                        $newSchoolTeacher->session = $currentSession;
                        $newSchoolTeacher->save();

                        //Preparing the the response
                        $response['status'] = 'success';
                        $response['message'] = 'Teacher has been created successfully!';
                        $response['code'] = 200;
                        $response['data'] = array('user_id' => $newUser->id);

                        //Sending the response
                        return response($response)->header('Content-Type', 'application/json');

                        break;
                    }
                case '8': {
                        //STUDENT
                        //Student inputs
                        $student_num = $req['student_no'];
                        $student_enroll_year = $req['student_enroll_year'];
                        $student_grade_id = $req['student_grade_id'];
                        $student_class_id = $req['student_class_id'];

                        //Validate the inputs
                        $validator = Validator::make($req->all(), [
                                    'student_no' => 'required',
                                    'student_enroll_year' => 'required',
                                    'student_grade_id' => 'required',
                                    'student_class_id' => 'required',
                        ]);

                        //Validate the inputs
                        if ($validator->fails()) {

                            $errors = $validator->errors();

                            // Bake the response with errors
                            $response['error'] = $errors;

                            // Serve the response
                            return response($response)->header('Content-Type', 'application/json');
                        }
                        //Prepare login_id and password
                        $laststudent = MencoStudent::orderby('created_at', 'desc')
                                ->first();

                        if ($laststudent->login_id) {
                            $loginid = substr($laststudent->login_id, 2);
                            $loginid = (int) ($loginid + 1);
                            $newlogin_id = 'st' . (string) $loginid; //Generating new login_id for student ex stXXXX
                            $newPassword = \Hash::make('menco123');
                        } else {
                            $newlogin_id = 'st1001';
                            $newPassword = \Hash::make('menco123');
                        }
                        $newStudent = new MencoStudent;
                        $newStudent->login_id = $newlogin_id;
                        $newStudent->password = $newPassword;
                        $newStudent->name = $name;
                        $newStudent->student_no = $student_num;
                        $newStudent->grade_id = $student_grade_id;
                        $newStudent->class_id = $student_class_id;
                        $newStudent->school_id = $school_id;
                        $newStudent->enroll_year = $student_enroll_year;
                        $newStudent->save();

                        //Preparing the the response
                        $response['status'] = 'success';
                        $response['message'] = 'Student has been created successfully!';
                        $response['code'] = 200;
                        $response['data'] = array('student_login_id' => $newStudent->login_id);

                        //Sending the response
                        return response($response)->header('Content-Type', 'application/json');
                        break;
                    }
                case '9': {
                        //SUB-MENCO
                        //Store the role in menco_user_role table
                        $newUserRole = new MencoUserRole;
                        $newUserRole->user_id = $newUser->id; //store the new user id
                        $newUserRole->role_id = 9;
                        $newUserRole->save();

                        //Preparing the the response
                        $response['status'] = 'success';
                        $response['message'] = 'Sub admin has been created successfully!';
                        $response['code'] = 200;
                        $response['data'] = array('user_id' => $newUser->id);

                        //Sending the response
                        return response($response)->header('Content-Type', 'application/json');
                        break;
                    }
                default: {
                        # code...
                        break;
                    }
            }
        } else {
            //Preparing the the response
            $response['message'] = 'Invalid parameters';
            $response['code'] = 417;

            //Sending the response
            return response($response)->header('Content-Type', 'application/json');
        }
    }
}

}