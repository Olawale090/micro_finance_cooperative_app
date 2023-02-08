<?php 

    class user{
        public function props(){
            $this->name = "Olawale";
            $this->school = "LAUTECH";
            $this->phone_number = '08168612448';
        }

        public function call_name():string{
            return $this->name;
        }

        public function call_school():string{
            return $this->school;
        }

        public function call_phone():string{
            return $this->phone_number;
        }

    }

    class dto{
        public function props(user $main){
            $this->name = $main->name;
            $this->school = $main->school;
            $this->phone = $main->phone_number;
        }

        public function get_name():string{
            return $this->name;
        }

        public function get_school():string{
            return $this->school;
        }

        public function get_phone():string{
            return $this->phone;
        }

    }

    $user = new user();
    $user->props();

    $dto = new dto();
    $dto->props($user);

    class helper{
        public function fetcher (dto $dto){
            
            echo json_encode([ 
                "data"=>[
                    "DTO_name" => $dto->get_name(),
                    "DTO_school" => $dto->get_school(),
                    "DTO_phone_number" => $dto->get_phone()
                ],
                
                "status"=>200

            ]);
            
        }
    }

    $helper = new helper();
    $helper->fetcher($dto);

   



?>