<?php

namespace App\Http\Controllers;

use App\UserPersonalDetails;
use App\UserAcademics;
use App\UserExperiences;
use App\UserTechnical;
use App\UserRatings;
use App\UserHGMI;
use App\TQCategoryDetails;
use Session;

use App\UserTests;
use App\IQ;
use App\EQ;
use App\AQ;

use App\LinkedinProfiles;
use App\UserIQ;
use App\UserEQ;
use App\UserAQ;

use App\ResumeClassification;

use Illuminate\Http\Request;

class UserController extends Controller
{


    public function redirectDashboard(){
        $id = Session::get('user_id');
//        echo $id;

        $check_data = UserPersonalDetails::where('user_id', $id)->get();
//        echo $check_data;
        if(sizeof($check_data) == 0){
            return view('template/preuser');
        }else{
            $user_test_details = UserTests::where('user_id',$id)->get();
//        $user_test_array = json_decode(json_encode($user_test_details), true);
//         print_r($user_test_array);
            return view('template/dashboard')->with('user_test_details',$user_test_details);

        }

    }


    public function skills_view(){
//        echo "hi";

        $id = Session::get('user_id');
//        echo $id;
//        exit;
        $user_detail=UserTechnical::where('user_id',$id)->where('level_1_test_given',0)->get();
//        if($user_detail){
//            echo "sn";
//        }
       // echo "<pre>";
       // print_r($user_detail);
       // exit;
        $size=sizeof($user_detail);
//        echo $size;
        $count=0;
        $skill_array=array();
        $skill_id_array = array();
        while($count!=$size)
        {

            $skill_name = TQCategoryDetails::where('tq_category_details_id', $user_detail[$count]->tq_category_details_id)->pluck('sub_category')->first();
            $lan=$skill_name;
            $skill_id = $user_detail[$count]->tq_category_details_id;
//            echo $lan;
            $count+=1;
            array_push($skill_array,$lan);
            array_push($skill_id_array, $skill_id);
        }
//        print_r($skill_array);
//        exit;
        $skill_set=implode(",",$skill_array);
        $skill_id_array_set=implode(",",$skill_id_array);
//        print_r($skill_set);
        $skill_set=explode(",",$skill_set);
        $skill_id_array_set=explode(",",$skill_id_array_set);
//        print_r($skill_set);
//        exit;
//        echo "snj";
//        $ab=sizeof($skill_set);
//        echo $ab;
//        echo $skill_set[0];

        $EmptyTestArray1 = array_filter($skill_set);
        $EmptyTestArray2 = array_filter($skill_id_array_set);

        if (!empty($EmptyTestArray1))
        {
            // do some tests on the values in $ArrayOne
            echo "exists";
//            print_r($EmptyTestArray1);
//            print_r($EmptyTestArray2);
            // print_r($skill_set);
            // exit;
            return view('template/tq_instructions')->with("skill_set",$skill_set)->with("skill_id_array_set", $skill_id_array_set);


        }
        else
        {
            // Likely not to need an else,
            // but could return message to user "you entered nothing" etc etc
//            echo "empty";
//            echo "test done";

            $user_id = Session::get('user_id');
            $user_test = UserTests::where('user_id',$user_id)->update(['tq_given'=>1]);
            return app('App\Http\Controllers\UserController')->redirectDashboard();


        }
    }

    public function advance_skills_view(){
//        echo "hi";

        $id = Session::get('user_id');
//        echo $id;
//        exit;
        $user_detail=UserTechnical::where('user_id',$id)->where('level_1_test_given',1)->where('level_2_eligible',1)->where('level_2_test_given',0)->get();
//        if($user_detail){
//            echo "sn";
//        }
//        echo "<pre>";
//        print_r($user_detail);
        $size=sizeof($user_detail);
//        echo $size;
        $count=0;
        $skill_array=array();
        $skill_id_array = array();
        while($count!=$size)
        {

            $skill_name = TQCategoryDetails::where('tq_category_details_id', $user_detail[$count]->tq_category_details_id)->pluck('sub_category')->first();
            $lan=$skill_name;
            $skill_id = $user_detail[$count]->tq_category_details_id;
//            echo $lan;
            $count+=1;
            array_push($skill_array,$lan);
            array_push($skill_id_array, $skill_id);
        }
//        print_r($skill_array);
//        exit;
        $skill_set=implode(",",$skill_array);
        $skill_id_array_set=implode(",",$skill_id_array);
//        print_r($skill_set);
        $skill_set=explode(",",$skill_set);
        $skill_id_array_set=explode(",",$skill_id_array_set);
//        print_r($skill_set);
//        exit;
//        echo "snj";
//        $ab=sizeof($skill_set);
//        echo $ab;
//        echo $skill_set[0];

        $EmptyTestArray1 = array_filter($skill_set);
        $EmptyTestArray2 = array_filter($skill_id_array_set);

        if (!empty($EmptyTestArray1))
        {
            // do some tests on the values in $ArrayOne
            echo "exists";
//            print_r($EmptyTestArray1);
//            print_r($EmptyTestArray2);
            return view('template/advance_tq_instructions')->with("skill_set",$skill_set)->with("skill_id_array_set", $skill_id_array_set);


        }
        else
        {
            // Likely not to need an else,
            // but could return message to user "you entered nothing" etc etc
//            echo "empty";
//            echo "test done";
//            $user_id = Session::get('user_id');
//            $user_test = UserTests::where('user_id',$user_id)->update('tq_given',1);
            return app('App\Http\Controllers\UserController')->redirectDashboard();

        }
    }



    public function storeMedia(Request $request)
    {
        $this->validate($request, [
            'image_filename' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
//        |max:2048

        // echo "in store media func";
        $user_id = Session::get('user_id');

        if ($request->hasFile('image_filename')) {
            $image = $request->file('image_filename');
            $image_name = $user_id.'.'.$image->getClientOriginalExtension();
//            echo $name;
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $image_name);
//        $this->save();
        }

        if ($request->hasFile('resume_filename')) {
            $resume = $request->file('resume_filename');
            $resume_name = $user_id.'.'.$resume->getClientOriginalExtension();
//            echo $name;
            $destinationPath = public_path('/resumes');
            $resume->move($destinationPath, $resume_name);
//        $this->save();
        }

        if($_POST['use_resume']){
            $endpoint = "http://aef4d9e6e63d.ngrok.io/resume_api";
            $client = new \GuzzleHttp\Client();



            // $response = $client->request('POST', $endpoint, ['query' => [

            //     // 'key2' => $value,
            // ]]);
            // print_r($request->file('image_filename'));
            // exit();
            $file = $request->file('resume_filename');
//        $name = $file->getClientOriginalName();
            $name = $user_id.'.'.$file->getClientOriginalExtension();
//        $path = 'C:\\Users\\Ujala Jha\\Downloads\\';
            $path = public_path('resumes/');
            $response =  $client->request('POST', $endpoint, [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => file_get_contents($path . $name),
                        'filename' => $name
                    ]
                ],
            ]);

            // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

            // $statusCode = $response->getStatusCode();
            // $content = $response->getBody();

            // or when your server returns json
            $content = json_decode($response->getBody(), true);
//        echo "<pre>";
//        print_r($content);
//        print_r($content['Candidate Details'][0]['Email']);
            $education = $content['Candidate Details'][0]['Education'];
            $email = $content['Candidate Details'][0]['Email'];
            $experience = $content['Candidate Details'][0]['Experience'];
            $name = $content['Candidate Details'][0]['Name'];
            $phone_no = $content['Candidate Details'][0]['Phone No'];
            $qualification = $content['Candidate Details'][0]['Qualification Tags'];
            $skillset = $content['Candidate Details'][0]['Skillset'];
            $skillset = strtoupper($skillset);

//        echo $skillset;
//        print_r(explode(',',$skillset));
            $skillset = explode(',',$skillset);
//        print_r(explode(',',$qualification));
            $qualification = explode(',',$qualification);
//        print_r(explode(',',$experience));
            $experience = explode(',',$experience);
            $name = explode(' ', $name);
//        exit();
            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //   CURLOPT_URL => "https://68efb60e1c22.ngrok.io/resume_api",
            //   CURLOPT_RETURNTRANSFER => true,
            //   CURLOPT_ENCODING => "",
            //   CURLOPT_MAXREDIRS => 10,
            //   CURLOPT_TIMEOUT => 0,
            //   CURLOPT_FOLLOWLOCATION => true,
            //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //   CURLOPT_CUSTOMREQUEST => "POST",
            //   CURLOPT_POSTFIELDS => array('url' => 'https://www.jobvacancyresult.com/storage/users/resumes/5833_02_Jatin_Acharya%20-%20JATIN%20ACHARYA.pdf'),
            // ));

            // $response = curl_exec($curl);
            // curl_close($curl);
            // // $response = $connection -> getData();

            // // get rid of the extra NULs
            // // $response = str_replace(chr(0), '', $response);
            // // $response = rtrim($response, "\0");
            // // print_r($response->toJson());
            // $response = stripslashes(html_entity_decode($response));
            // $response = utf8_encode($response);
            // $response = substr($response, 1);
            // $response = substr($response, 0, -1);

            // $var=json_decode($response);
            // print_r($var);
            // print_r($var[0]->Email);
            // // print_r(json_last_error());
            // exit();

            return view('template/user')->with('image_name', $image_name)
                                                ->with('resume_name', $resume_name)
                                                ->with('education', $education)
                                                ->with('email', $email)
                                                ->with('experience', $experience)
                                                ->with('name', $name)
                                                ->with('phone_no', $phone_no)
                                                ->with('qualification', $qualification)
                                                ->with('skillset',$skillset);
        }else{
            return view('template/user')->with('image_name', $image_name)
                                                ->with('resume_name', $resume_name);
        }


    }


    public function jobrecommendation()
    {

        //give this string
        // $skills="python-advance,java-basic,machine learning-advance,data science-intermediate,r-intermediate,business analytics-intermediate,sql-advance";
        $skills='';

        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //   CURLOPT_URL => "http://127.0.0.1:5000/api",
        //   CURLOPT_RETURNTRANSFER => true,
        //   CURLOPT_ENCODING => "",
        //   CURLOPT_MAXREDIRS => 10,
        //   CURLOPT_TIMEOUT => 0,
        //   CURLOPT_FOLLOWLOCATION => true,
        //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //   CURLOPT_CUSTOMREQUEST => "POST",
        //   CURLOPT_POSTFIELDS =>"{\r\n        \"skills\": \"python-advance,java-basic,machine learning-advance,data science-intermediate,r-intermediate,business analytics-intermediate,sql-advance\" \r\n}",
        //   CURLOPT_HTTPHEADER => array(
        //     "Content-Type: application/json"
        //   ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // echo $response;

        $json_string = '{"job profiles":["python/django senior software","data scientist -","database specialist  ","data software engineer","data analytics, nj","sr. data scientist","analyst - python","esri arcgis administrator","mobile sdet  ","data analystics engineer","lead data scientist","senior database administrator"],"jobs":[{"from":"Indeed","job_company":"StartUs Insights","job_id":"93ad1b7f9420a729","job_link":"https://www.indeed.co.in/jobs?q=python/django+senior+software&l=India&start=10&vjk=93ad1b7f9420a729","job_location":"Bengaluru, Karnataka","job_summary":"— Solid understanding of software development principles and best practices.\nBuilding data applications in a product company,.\nWHAT YOU GET IN RETURN: *.","job_title":"Senior Python Developer","posted_date":"21 days ago"},{"from":"Indeed","job_company":"Techversant Infotech Pvt. Ltd.","job_id":"672b77efc079ca85","job_link":"https://www.indeed.co.in/jobs?q=python/django+senior+software&l=India&start=10&vjk=672b77efc079ca85","job_location":"Thiruvananthapuram, Kerala","job_summary":"MVC software pattern and frameworks.\nWork as a member of a team or on their own to deliver high quality and maintainable software solutions, to strict deadlines…","job_title":"Sr. Software Engineer / Software Engineer - ColdFusion","posted_date":"30+ days ago"},{"from":"Indeed","job_company":"HP","job_id":"f19117ec92fc2221","job_link":"https://www.indeed.co.in/jobs?q=lead+data+scientist&l=India&start=10&vjk=f19117ec92fc2221","job_location":"Bengaluru, Karnataka","job_summary":"O Fluent in structured and unstructured data and modern data transformation methodologies.\nO Leads a project team of data science professionals.","job_title":"Data Scientist Sales & Channel","posted_date":"25 days ago"}]}';
        echo "<pre>";
        print_r(json_decode($json_string,true));
        exit();


    }

    //NOTE: candidaterecommendation function shifted to company controller

    public function learningrecommendation(){
        $data['name']="Ujala Jha";
        $data['skills']="sql-advance,photoshop-basic',graphql-intermediate,ajax-basic,bootstrap-intermediate,css3-intermediate,angularjs-basic";
        $json=json_encode($data);
        // print_r($json);
        // exit();

        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //   CURLOPT_URL => "http://localhost:5002/course_api",
        //   CURLOPT_RETURNTRANSFER => true,
        //   CURLOPT_ENCODING => "",
        //   CURLOPT_MAXREDIRS => 10,
        //   CURLOPT_TIMEOUT => 0,
        //   CURLOPT_FOLLOWLOCATION => true,
        //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //   CURLOPT_CUSTOMREQUEST => "POST",
        //   CURLOPT_POSTFIELDS =>$json,
        //   CURLOPT_HTTPHEADER => array(
        //     "Content-Type: application/json"
        //   ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        echo "<pre>";

        // print_r(json_decode($response, TRUE));
        print_r(json_decode('{
              "recommended_courses": [
                {
                  "content_duration": 3.0,
                  "course_id": 16151,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 23,
                  "num_reviews": 147,
                  "num_subscribers": 7867,
                  "price": 20,
                  "published_timestamp": "2012-05-15T18:03:43Z",
                  "subject": "Web Development",
                  "title": "AJAX Development",
                  "url": "https://www.udemy.com/ajax-development/"
                },
                {
                  "content_duration": 3.5,
                  "course_id": 657734,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 26,
                  "num_reviews": 89,
                  "num_subscribers": 12368,
                  "price": 195,
                  "published_timestamp": "2015-11-13T22:03:15Z",
                  "subject": "Web Development",
                  "title": "Complete AJAX Course: Learn AJAX Techniques Using Bootstrap",
                  "url": "https://www.udemy.com/ajaxcourse/"
                },
                {
                  "content_duration": 0.5666666666666667,
                  "course_id": 955138,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 9,
                  "num_reviews": 23,
                  "num_subscribers": 1241,
                  "price": 35,
                  "published_timestamp": "2016-09-10T20:59:38Z",
                  "subject": "Web Development",
                  "title": "AJAX :basics for beginners",
                  "url": "https://www.udemy.com/ajaxbasics/"
                },
                {
                  "content_duration": 4.0,
                  "course_id": 746790,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 60,
                  "num_reviews": 30,
                  "num_subscribers": 1554,
                  "price": 20,
                  "published_timestamp": "2016-02-02T18:36:12Z",
                  "subject": "Web Development",
                  "title": "JavaScript, jQuery and Ajax",
                  "url": "https://www.udemy.com/ajax-calls-the-simplest-way/"
                },
                {
                  "content_duration": 2.0,
                  "course_id": 691760,
                  "is_paid": true,
                  "level": "Beginner Level",
                  "num_lectures": 18,
                  "num_reviews": 5,
                  "num_subscribers": 755,
                  "price": 65,
                  "published_timestamp": "2016-01-25T17:57:24Z",
                  "subject": "Web Development",
                  "title": "Ajax  for Beginners: A Very Basic Introduction",
                  "url": "https://www.udemy.com/ajax-for-beginners-a-very-basic-introduction/"
                },
                {
                  "content_duration": 14.0,
                  "course_id": 304490,
                  "is_paid": true,
                  "level": "Intermediate Level",
                  "num_lectures": 86,
                  "num_reviews": 231,
                  "num_subscribers": 4183,
                  "price": 35,
                  "published_timestamp": "2014-10-12T06:29:05Z",
                  "subject": "Web Development",
                  "title": "A 13 Hour SQL Server 2014 /ASP.NET/CSS/C#/jQuery Course",
                  "url": "https://www.udemy.com/learnsqlwithsqlserver2014/"
                },
                {
                  "content_duration": 5.5,
                  "course_id": 425084,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 98,
                  "num_reviews": 65,
                  "num_subscribers": 586,
                  "price": 60,
                  "published_timestamp": "2015-03-23T00:20:29Z",
                  "subject": "Web Development",
                  "title": "Administering Microsoft SQL Server 2012 Databases - 70-462",
                  "url": "https://www.udemy.com/administering-microsoft-sql-server-2012-databases-70-462/"
                },
                {
                  "content_duration": 5.0,
                  "course_id": 425086,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 90,
                  "num_reviews": 148,
                  "num_subscribers": 1142,
                  "price": 85,
                  "published_timestamp": "2015-05-01T23:08:38Z",
                  "subject": "Web Development",
                  "title": "Implementing a Data Warehouse with SQL Server 2012 ",
                  "url": "https://www.udemy.com/implementing-a-data-warehouse-with-sql-server-2012/"
                },
                {
                  "content_duration": 6.5,
                  "course_id": 422012,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 115,
                  "num_reviews": 89,
                  "num_subscribers": 666,
                  "price": 85,
                  "published_timestamp": "2015-03-12T21:20:41Z",
                  "subject": "Web Development",
                  "title": "Querying Microsoft SQL Server 2012 - (Exam No. 70-461)",
                  "url": "https://www.udemy.com/querying-sql-server-2012-70-461/"
                },
                {
                  "content_duration": 5.5,
                  "course_id": 1052304,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 77,
                  "num_reviews": 102,
                  "num_subscribers": 11285,
                  "price": 195,
                  "published_timestamp": "2017-02-07T17:20:39Z",
                  "subject": "Web Development",
                  "title": "JavaScript For Beginners : Learn JavaScript From Scratch",
                  "url": "https://www.udemy.com/javascript-course-for-beginners/"
                },
                {
                  "content_duration": 4.0,
                  "course_id": 8325,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 82,
                  "num_reviews": 253,
                  "num_subscribers": 12458,
                  "price": 20,
                  "published_timestamp": "2011-09-09T15:28:59Z",
                  "subject": "Web Development",
                  "title": "HTML Tutorial: HTML & CSS for Beginners",
                  "url": "https://www.udemy.com/learn-html5/"
                },
                {
                  "content_duration": 2.0,
                  "course_id": 15285,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 17,
                  "num_reviews": 306,
                  "num_subscribers": 25854,
                  "price": 20,
                  "published_timestamp": "2012-04-08T05:12:43Z",
                  "subject": "Web Development",
                  "title": "HTML Workshop",
                  "url": "https://www.udemy.com/html-workshop/"
                },
                {
                  "content_duration": 3.0,
                  "course_id": 1110756,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 27,
                  "num_reviews": 15,
                  "num_subscribers": 2401,
                  "price": 20,
                  "published_timestamp": "2017-02-22T17:36:27Z",
                  "subject": "Web Development",
                  "title": "HTML Tutorials : HTML Code for Website Creating",
                  "url": "https://www.udemy.com/html-code-for-website/"
                },
                {
                  "content_duration": 1.0,
                  "course_id": 958982,
                  "is_paid": true,
                  "level": "Beginner Level",
                  "num_lectures": 25,
                  "num_reviews": 20,
                  "num_subscribers": 785,
                  "price": 25,
                  "published_timestamp": "2016-09-15T18:06:58Z",
                  "subject": "Web Development",
                  "title": "JavaScript : JavaScript Awesomeness",
                  "url": "https://www.udemy.com/javascript-javascript/"
                },
                {
                  "content_duration": 5.0,
                  "course_id": 529828,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 36,
                  "num_reviews": 25,
                  "num_subscribers": 638,
                  "price": 95,
                  "published_timestamp": "2015-06-17T22:23:31Z",
                  "subject": "Business Finance",
                  "title": "Python for Trading & Investing",
                  "url": "https://www.udemy.com/python-for-trading-investing/"
                },
                {
                  "content_duration": 4.0,
                  "course_id": 16646,
                  "is_paid": true,
                  "level": "All Levels",
                  "num_lectures": 53,
                  "num_reviews": 217,
                  "num_subscribers": 35267,
                  "price": 50,
                  "published_timestamp": "2012-04-25T00:01:43Z",
                  "subject": "Web Development",
                  "title": "Web Programming with Python",
                  "url": "https://www.udemy.com/web-programming-with-python/"
                }
              ]
            }'));
        exit();

    }
    public function githubjobs(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://jobs.github.com/positions.json",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }
    public function blogrecommendation(){

        $data['name']='Maya';
        $data['skills']='python,graphql,chatbot,bootstrap,finance,angularjs,machine learning,ai,rest';
        $json=json_encode($data);
        // print_r($json);
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://127.0.0.1:5000/blog_api",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>$json,
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo "<pre>";
        print_r(json_decode($response));


    }

    public function store(Request $request)
    {
        //
//        echo "<pre>";
//        print_r($_POST);
//        exit;

        $user_id = Session::get('user_id');

        $user = new UserPersonalDetails;
        $user->user_id = $user_id;
        $user->first_name = $_POST['first_name'];
        $user->last_name = $_POST['last_name'];
        $user->age = $_POST['age'];
        $user->gender = $_POST['gender'];
        $user->email_id = $_POST['email_id'];
        $user->contact_number = $_POST['contact_number'];
        $user->address = $_POST['address'];
        $user->city = $_POST['city'];
        $user->country = $_POST['country'];
        $user->postal_code = $_POST['postal_code'];



        $string='';
        foreach ($_POST['skills'] as $value){
            $string .=  $value.',';
        }
        $user->skills = $string;
        $user->linkedin_id = $_POST['linkedin_id'];
        $user->github_id = $_POST['github_id'];
        $user->other_links = $_POST['other_links'];
        $user->image_filename = $_POST['image_name'];
        $user->resume_filename = $_POST['resume_name'];

        $user->save();


        //traversy

//        if($request->hasFile('resume_filename')){
//            // Get filename with the extension
//            $filenameWithExt = $request->file('resume_filename')->getClientOriginalName();
//            echo $filenameWithExt;
//            // Get just filename
//            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
//            // Get just ext
//            $extension = $request->file('resume_filename')->getClientOriginalExtension();
//            // Filename to store
//            $fileNameToStore= $filename.'_'.time().'.'.$extension;
//            // Upload Image
//            $path = $request->file('resume_filename')->storeAs('public/resume', $fileNameToStore);
//        } else {
//            $fileNameToStore = 'noimage.jpg';
//            echo "no file";
//        }

        //traversy



//
//        $file = $request->file('resume_filename');
//        //Display File Name
//        echo 'File Name: '.$file->getClientOriginalName();
//        echo '<br>';
//
//        //Display File Extension
//        echo 'File Extension: '.$file->getClientOriginalExtension();
//        echo '<br>';
//
//        //Display File Real Path
//        echo 'File Real Path: '.$file->getRealPath();
//        echo '<br>';
//
//        //Display File Size
//        echo 'File Size: '.$file->getSize();
//        echo '<br>';
//
//        //Display File Mime Type
//        echo 'File Mime Type: '.$file->getMimeType();
//
//        //Move Uploaded File
//        $destinationPath = 'uploads';
//        $file->move($destinationPath,$file->getClientOriginalName());






        foreach ($_POST['skills'] as $value){
            $user_technical_details = new UserTechnical;

            $tq_category = TQCategoryDetails::where('sub_category', $value)->pluck('tq_category_details_id')->first();

//            echo $tq_category."-".$value;

            $user_technical_details->user_id = $user_id;
            $user_technical_details->tq_category_details_id = $tq_category;

            $user_technical_details->save();

            $user_technical_details->user_id = "";
            $user_technical_details->tq_category_details_id = "";


            $user_technical_details = null;

        }



        foreach ($_POST['skills'] as $value){
            $user_ratings_details = new UserRatings;

            $user_ratings_details->user_id = $user_id;
            $user_ratings_details->language = $value;

            $user_ratings_details->save();

            $user_ratings_details->user_id = "";
            $user_ratings_details->language = "";


            $user_ratings_details = null;


        }

//        return view('/template/user')->with('user_id',$user_id);




//    }


//    public  function storeAcademics(){

        $user_academics = new UserAcademics;
//        echo "<pre>";
//        print_r($_POST);
//        exit;
        $user_id = Session::get('user_id');

        $user_academics->user_id = $user_id;
        $user_academics->x_school_name = $_POST['x_school_name'];
        $user_academics->x_board_name = $_POST['x_board_name'];
        $user_academics->x_year_of_completion = $_POST['x_year_of_completion'];
        $user_academics->is_x_gpa_percentage = $_POST['is_x_gpa_percentage'];
        $user_academics->x_gpa_percentage = $_POST['x_gpa_percentage'];

        $user_academics->xii_school_name = $_POST['xii_school_name'];
        $user_academics->xii_board_name = $_POST['xii_board_name'];
        $user_academics->xii_year_of_completion = $_POST['xii_year_of_completion'];
        $user_academics->is_xii_gpa_percentage = $_POST['is_xii_gpa_percentage'];
        $user_academics->xii_gpa_percentage = $_POST['xii_gpa_percentage'];

        $user_academics->ug_university_name = $_POST['ug_university_name'];
        $user_academics->ug_college_name = $_POST['ug_college_name'];
        $user_academics->ug_course_name = $_POST['ug_course_name'];
        $user_academics->ug_year_of_graduation = $_POST['ug_year_of_graduation'];
        $user_academics->is_ug_gpa_percentage = $_POST['is_ug_gpa_percentage'];
        $user_academics->ug_average_gpi = $_POST['ug_average_gpi'];

        $user_academics->save();


//        return view('/template/user')->with('user_id',$user_id);

//    }

//    public function storeInternship(){


        $user_id = Session::get('user_id');


        $size = count($_POST['company_name']);
//        echo "<pre>";
//        print_r($user_experience->company_name);
//        exit;
        for($i=0; $i<$size; $i++){
//            echo $i;
//            echo $_POST['company_name'][$i];
            $user_experience = new UserExperiences;

            $user_experience->user_id = $user_id;
            $user_experience->is_internship_project = 1;
            $user_experience->company_name = $_POST['company_name'][$i];
            $user_experience->project_name = $_POST['project_name'][$i];
            $user_experience->role = $_POST['role'][$i];
            $user_experience->duration = $_POST['duration'][$i];
            $user_experience->domain = $_POST['domain'][$i];
            $user_experience->tech_stack = $_POST['tech_stack'][$i];


            $user_experience->save();

            $user_experience->company_name = "";
            $user_experience->project_name = "";
            $user_experience->role = "";
            $user_experience->duration = "";
            $user_experience->domain = "";
            $user_experience->tech_stack = "";

            $user_experience = null;




//            echo "ss";

        }

//        return view('/template/user');

//    }

//    public function storeProject(){
        $size = count($_POST['project_name']);
//        $user_project->project_name;
        $user_id = Session::get('user_id');

//        for($i=0; $i<$size; $i++){
//            $user_project = new UserExperiences;
//
//            $user_project->user_id = $user_id;
//            $user_project->project_name = $_POST['project_name'][$i];
//            echo $user_project->project_name;
//            $user_project->role = $_POST['role'][$i];
//            $user_project->domain = $_POST['domain'][$i];
//            $user_project->duration = $_POST['duration'][$i];
//            $user_project->tech_stack = $_POST['tech_stack'][$i];
//
//            $user_project->save();
////            echo $user_project;
////            echo $user_project->project_name;
//            $user_project->project_name = "";
//            $user_project->role = "";
//            $user_project->domain = "";
//            $user_project->duration = "";
//            $user_project->tech_stack = "";
//
//            echo "hello";
//
////            $user_project = null;
//
//        }


        $i = 0;
//        print_r($_POST);
//        echo "<br>".$_POST["project_name"][0];
//        echo "<br>".count($_POST["project_name"]);
//        for($i=0; $i<count($_POST["project_name"]); $i++){
//            echo $i;
//            $user_project = new UserExperiences;
//            $user_project->project_name = $_POST['project_name'][$i];
//            $user_project->role = $_POST['role'][$i];
//            $user_project->domain = $_POST['domain'][$i];
//            $user_project->duration = $_POST['duration'][$i];
//            $user_project->tech_stack = $_POST['tech_stack'][$i];
//            $user_project->save();
//        }

        $i=count($_POST["project_name"]);
        while($i>0){
            $i--;
//            echo $i;
            $user_project = new UserExperiences;
            $user_project->user_id = $user_id;
            $user_project->project_name = $_POST['project_name'][$i];
            $user_project->role = $_POST['role'][$i];
            $user_project->domain = $_POST['domain'][$i];
            $user_project->duration = $_POST['duration'][$i];
            $user_project->tech_stack = $_POST['tech_stack'][$i];
            $user_project->save();

        }

//        foreach ($_POST as $post){
//            echo $key[$i];
//            echo $value[$i];
//            $i++;
//            echo $_POST["project_name"];

//            $i++;
//            print_r($_POST);
//        }


//        return view('/template/user');
        $user_test_details = UserTests::where('user_id',$user_id)->get();
//        $user_test_array = json_decode(json_encode($user_test_details), true);
//         print_r($user_test_array);
        return view('template/dashboard')->with('user_test_details',$user_test_details);


    }

    public function saveTestScore(){

        $id = Session::get('user_id');

        $iq_score = $_POST['iq_score'];
        $user_iq_score = new UserIQ;
        $user_iq_score->user_id = $id;
        $user_iq_score->iq_score = $iq_score;
        $user_iq_score->save();

        $user_tech_detail=UserExperiences::where('user_id',$id)->where('is_internship_project',1)->get();
//        echo $user_tech_detail;
        $lang=$user_tech_detail[0]['tech_stack'];
//        echo $lang;
        $tech=UserRatings::where('user_id',$id)->where('language',$lang)->update(['internship_star'=>1]);
//        exit;


//        $user_rating_detail=UserRatings::where('user_id',$id)->get();
//
//                $int=$user_rating_detail[0]['internship_star'];
//                $exp=$user_rating_detail[0]['project_star'];
//                $tec=$user_rating_detail[0]['technical_star'];
//                $tot=$int+$exp+$tec;
//                echo $int.$exp.$tec;
//        echo "<br>";
//        echo $tot;
//                $tech=UserRatings::where('user_id',$id)->where('language',$lang)->update(['total_star'=>$tot]);

//                echo $int;
        $user_tech_detail=UserExperiences::where('user_id',$id)->where('is_internship_project',0)->get();
        $lang=$user_tech_detail[0]['tech_stack'];
        $tech=UserRatings::where('user_id',$id)->where('language',$lang)->update(['project_star'=>1]);
//                exit;
//        $user_tech_detail=UserExperiences::where('user_id',$id)->where('is_internship_project',0)->get();


//                exit;

        $self_awareness = $_POST['self_awareness'];
        $self_control = $_POST['self_control'];
        $achievement_orientation = $_POST['achievement_orientation'];
        $positive_outlook = $_POST['positive_outlook'];
        $inspirational_leadership = $_POST['inspirational_leadership'];
        $social_awareness = $_POST['social_awareness'];
        $user_eq_score = new UserEQ;
        $user_eq_score->user_id = $id;
        $user_eq_score->eq_self_awareness = $self_awareness;
        $user_eq_score->eq_self_control = $self_control;
        $user_eq_score->eq_achievement_orientation = $achievement_orientation;
        $user_eq_score->eq_positive_outlook = $positive_outlook;
        $user_eq_score->eq_inspirational_leadership = $inspirational_leadership;
        $user_eq_score->eq_social_awareness = $social_awareness;
        $user_eq_score->save();



        $persistence = $_POST['persistence'];
        $boldness = $_POST['boldness'];
        $complexity = $_POST['complexity'];
        $abstraction = $_POST['abstraction'];
        $curiosity = $_POST['curiosity'];
        $user_aq_score = new UserAQ;
        $user_aq_score->user_id = $id;
        $user_aq_score->aq_persistence = $persistence;
        $user_aq_score->aq_boldness = $boldness;
        $user_aq_score->aq_complexity = $complexity;
        $user_aq_score->aq_abstraction = $abstraction;
        $user_aq_score->aq_curiosity = $curiosity;
        $user_aq_score->save();
//        echo $iq_score;
//        exit;
        return view('/template/performance')->with("iq_score",$iq_score);


    }
    
    public function view_profile(){
        $id=Session::get('user_id');
//        echo $id;
        $user_details=UserPersonalDetails::where('user_id',$id)->get();
        $user_tq_details = UserTechnical::where('user_id', $id)->get('tq_category_details_id');
//        print_r($user_tq_details[0]['tq_category_details_id']);
        $skills_id_array = array();
        $count = 0;
        foreach ($user_tq_details as $skill){
            $skills_id_array[$count] = $skill['tq_category_details_id'];
            $count++;
        }
//        print_r($skills_id_array);
        $skills_name_array = array();
        $count = 0;
        foreach ($skills_id_array as $skill){
            $sub_category = TQCategoryDetails::where('tq_category_details_id', $skill)->get();
            $skills_name_array[$count] = $sub_category[0]['sub_category'];
            $count++;
        }
//        print_r($skills_name_array);

        $user_academics = UserAcademics::where('user_id', $id)->get();

        $user_internships = UserExperiences::where('user_id', $id)->where('is_internship_project', 1)->get();
//        print_r($user_internships);

        $user_projects = UserExperiences::where('user_id', $id)->where('is_internship_project', 0)->get();

//        exit;
        return view('/template/profile')->with("user_details",$user_details)
            ->with("user_tq_skills", $skills_name_array)
            ->with('user_academics', $user_academics)
            ->with('user_internships', $user_internships)
            ->with('user_projects', $user_projects);
        exit;





//        return view('/template/performance');

    }


    public function updateSkills(){
        $user_id = Session::get('user_id');

        foreach ($_POST['skills'] as $value){
            $user_technical_details = new UserTechnical;

            $tq_category = TQCategoryDetails::where('sub_category', $value)->pluck('tq_category_details_id')->first();

//            echo $tq_category."-".$value;

            $user_technical_details->user_id = $user_id;
            $user_technical_details->tq_category_details_id = $tq_category;

            $user_technical_details->save();

            $user_technical_details->user_id = "";
            $user_technical_details->tq_category_details_id = "";


            $user_technical_details = null;

        }



        foreach ($_POST['skills'] as $value){
            $user_ratings_details = new UserRatings;

            $user_ratings_details->user_id = $user_id;
            $user_ratings_details->language = $value;

            $user_ratings_details->save();

            $user_ratings_details->user_id = "";
            $user_ratings_details->language = "";


            $user_ratings_details = null;

        }

        return $this->view_profile();

    }


    public function pyexe(){

        echo "hello ";
//        exit;
        $commnad=escapeshellcmd('snj.py');
        $output=shell_exec($commnad);
        echo $output;
////        echo "here";
//        echo $commnad;
//


        $python = json_decode(`python snj.py`);
        var_dump($python);
    }


    public function filter_students(){

//        $id = Session::get('user_id');
        $branch=$_POST['branch'][0];
        // $exp=$_POST['experience'];
        // echo $exp;
//        exit;

//        echo $branch;
        $resume_class=ResumeClassification::where('branch_classification',$branch)->get();
//        echo $resume_class;

//        $s=$count
//        foreach($resume_class){
////        $data=UserPersonalDetails::where
//            echo "snj";
//        }
//        echo"hello";
//        exit;
        return view('/company/search_students')->with('resume_class',$resume_class);
//        exit;

    }

    public function linkedin_profile(){
        echo "here";
        $linkedin=LinkedinProfiles::all();
        // echo $linkedin;
        // exit;
        return view('/company/linkedin_students')->with('linkedin',$linkedin);

        // exit;
    }

    public function resume(){
        $endpoint = "http://localhost:5000/resume_api";
        $client = new \GuzzleHttp\Client();
        

        $response = $client->request('POST', $endpoint, ['query' => [
            'file' => $_POST['file'], 
            // 'key2' => $value,
        ]]);

        // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        // $statusCode = $response->getStatusCode();
        // $content = $response->getBody();

        // or when your server returns json
        $content = json_decode($response->getBody(), true);

        echo $content;
    }

        
        public function full_report(){
        $user_id=Session::get('user_id');
        echo $user_id;
        $eq=UserEQ::where('user_id',$user_id)->get();
        $aq=UserAQ::where('user_id',$user_id)->get();
        $iq=UserIQ::where('user_id',$user_id)->get();
        $tq=UserTechnical::where('user_id',$user_id)->get();
        $hgmi=UserHGMI::where('user_id',$user_id)->get();
        // echo "<pre>";
        // print_r($iq[0]);
        // print_r($aq[0]);
        // print_r($eq[0]);
        // print_r($tq[0]);

        return view('template/performance')->with('iq',$iq)->with('eq',$eq)->with('aq',$aq)->with('tq',$tq);


    }

    public function dummy_role(){
        $user_id=Session::get('user_id');
        $tq=UserTechnical::where('user_id',$user_id)->get();
        echo $tq[0];
        exit;
        $final_string="";
        foreach($tq as $tqa){
        $skill_id=TQCategoryDetails::where('tq_category_details_id',$tqa->tq_concept_details_id)->get();
        echo $skill_id[0]->sub_category;
        // if(level__score)
        $final_string=$skill_id[0]->sub_category."-".$tqa->level_1_score.",";
        echo $final_string;

    }
    // endforeach

        echo "<br>";

        echo $final_string;
    }

}
