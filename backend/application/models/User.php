<?php
class User extends CI_Model {

    private $_table ='user';

    public function findAll($filter=[])
    {
        //     $query = $this->db
        //     ->join('role',"role.id = $this->_table.role_id")
        //     ->select("$this->_table.*, role.name role_name")
        //     ->get($this->_table);
        //     return $query->result_array();

        $query = $this->db->from($this->_table)
        ->join('role',"role.id = $this->_table.role_id")
        ->where("$this->_table.company_code",$filter['company_code']);
            
        if(isset($filter['role'])){
                if($filter['role']==true){
                        $query->where("role.name",$filter['role']);
                }
                
        }
        if(isset($filter['admin'])){
                if($filter['admin']=='false'){
                        $query->where("role.name <>",'Admin');
                }
                
        }
        $query->select("$this->_table.*, role.name role_name");    

        return $query->get()->result_array();
    }

    public function find($id)
    {
            $query = $this->db->get_where($this->_table, array('id' => $id));
            return $query->row_array();
    }

    public function save($data)
    {
            return $this->db->insert($this->_table, [
                'username'=>$data['username'],
                'name'=>$data['name'],
                'password'=>password_hash($data['password'],PASSWORD_DEFAULT),
                'role_id'=>$data['role_id'],
                'company_code'=>$data['company_code'],
            ]);
    }

    public function update($id,$data)
    {
        if(isset($data['password'])){
                $data['password']=password_hash($data['password'],PASSWORD_DEFAULT);
        }
        return $this->db->update($this->_table, $data, array('id' =>$id));
    }

    public function destroy($id)
    {
            return $this->db->delete($this->_table, array('id' => $id));
    }

    public function getUser($where){
        $query = $this->db->from($this->_table)
        ->join('role',"role.id=$this->_table.role_id")
        ->where( $where)
        ->select("$this->_table.*, role.name role_name");
            return $query->get()->row_array(); 
    }

    public function isValid($data){
        $username = $data['username'];
        $password = $data['password'];
        // $company = $data['company_code'];

        if($this->getUser(['username'=>$username/*,'company_code'=>$company*/])){
                $hash = $this->getUser(['username'=>$username/*,'company_code'=>$company*/])['password'];

                if(password_verify($password,$hash)){
                        return true;
                }
        }
        

        

        return false;
    }

}