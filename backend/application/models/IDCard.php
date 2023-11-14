<?php
class IDCard extends CI_Model
{

    private $_table = 'id_card';

    public function findAll($filter = [])
    {
        $query = $this->db->from($this->_table)
            ->where("company_code", $filter['company_code']);

        if (isset($filter['induction_date'])) {
            $query->where("induction_date", $filter['induction_date']);
        }
        if (isset($filter['nik_from']) && isset($filter['nik_to'])) {
            $from = $filter['nik_from'];
            $to = $filter['nik_to'];
            if ($from > $to) {
                $temp = $from;
                $from = $to;
                $to = $temp;
            }
            $query->where("cast(nik as int) BETWEEN $from AND $to");
        }
        $query->select("$this->_table.*");

        return $query->get()->result_array();
    }

    public function findAllPaginated($filter = [])
    {
        $limit = isset($filter['limit']) ? $filter['limit'] : 10;

        $where = "and company_code = '$filter[company_code]'";
        array_walk($filter, function ($v, $k) use (&$where) {
            if (in_array($k, ['nik', 'name', 'cost_center', 'photo'])) {
                // $where[$k]=$v;
                $V = strtoupper($v);
                if ($k == 'photo' && $V == 'NULL') {
                    $where .= "and photo is null ";
                } else {
                    $where .= "and upper($k) like '%$V%' ";
                }
            }
        });
        array_walk($filter, function ($v, $k) use (&$where) {
            if (in_array($k, ['induction_date', 'created_at', 'last_updated_at'])) {
                // $where[$k]=$v;
                // $V=strtoupper($v);
                $where .= "and convert(varchar,$k,20) like '%$v%' ";
            }
        });
        // echo $where;exit;
        // array_walk($filter, function ($v, $k)use(&$where) {
        //         if(in_array($k,['year','date','month','week'])){
        //                 $where.="and $k = $v ";
        //         }
        // });


        $PAGE_SHOW = 5;

        $currPage = null;
        $current10 = null;
        $next5Ids = [];
        $nextId = null;
        $prev5Ids = [];
        $prevId = null;
        if (isset($filter['id']) && isset($filter['direction']) && isset($filter['page'])) {
            if ($filter['direction'] == 1) {
                $currPage = $filter['page'];

                $query = $this->db->query("select * from id_card where nik <= $filter[id] $where order by nik desc OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
                $query = $this->db->query("select nik id from id_card where nik <= $filter[id] $where order by nik desc OFFSET $nextOffset ROWS FETCH NEXT $nextLimit ROWS ONLY");
                $next41 = $query->result_array();

                $nextPage = $currPage + 1;
                array_walk($next41, function ($v, $k) use (&$next5Ids, $limit, &$nextPage) {
                    if ($k % $limit == 0) {
                        $v['direction'] = 1;
                        $v['page'] = $nextPage;
                        $v['limit'] = $limit;
                        array_push($next5Ids, $v);

                        $nextPage++;
                    }
                });
                $nextId = count($next5Ids) > 0 ? $next5Ids[0] : null;


                $prevOffset = $limit;
                $prevLimit = $limit * $PAGE_SHOW + 1;
                $query = $this->db->query("select nik id from id_card where nik > $filter[id] $where order by nik asc offset $prevOffset rows fetch next $prevLimit rows only");
                $prev51 = $query->result_array();

                $prevPage = $currPage - 1;
                array_walk($prev51, function ($v, $k) use (&$prev5Ids, $limit, &$prevPage) {
                    if ($k % $limit == 0) {
                        $v['direction'] = -1;
                        $v['page'] = $prevPage;
                        $v['limit'] = $limit;
                        array_unshift($prev5Ids, $v);

                        $prevPage--;
                    }
                });
                $prevId = count($prev5Ids) > 0 ? $prev5Ids[count($prev5Ids) - 1] : null;
            } elseif ($filter['direction'] == -1) {
                $currPage = $filter['page'];

                $query = $this->db->query("select * from id_card where nik < $filter[id] $where order by nik desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
                $query = $this->db->query("select nik id from id_card where nik < $filter[id] $where order by nik desc offset $nextOffset rows fetch next $nextLimit rows only");
                $next41 = $query->result_array();

                $nextPage = $currPage + 1;
                array_walk($next41, function ($v, $k) use (&$next5Ids, $limit, &$nextPage) {
                    if ($k % $limit == 0) {
                        $v['direction'] = 1;
                        $v['page'] = $nextPage;
                        $v['limit'] = $limit;
                        array_push($next5Ids, $v);

                        $nextPage++;
                    }
                });
                $nextId = count($next5Ids) > 0 ? $next5Ids[0] : null;


                $prevOffset = $limit;
                $prevLimit = $limit * $PAGE_SHOW + 1;
                $query = $this->db->query("select nik id from id_card where nik >= $filter[id] $where order by nik asc offset $prevOffset rows fetch next $prevLimit rows only");
                $prev51 = $query->result_array();

                $prevPage = $currPage - 1;
                array_walk($prev51, function ($v, $k) use (&$prev5Ids, $limit, &$prevPage) {
                    if ($k % $limit == 0) {
                        $v['direction'] = -1;
                        $v['page'] = $prevPage;
                        $v['limit'] = $limit;
                        array_unshift($prev5Ids, $v);

                        $prevPage--;
                    }
                });
                $prevId = count($prev5Ids) > 0 ? $prev5Ids[count($prev5Ids) - 1] : null;
            }
        } else if (isset($filter['id']) && isset($filter['direction'])) {
            if ($filter['direction'] == 1) {
                $query = $this->db->query("select * from id_card where nik <= $filter[id] $where order by nik desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();


                $nextOffset = $limit;
                $nextLimit = $limit + 1;
                $query = $this->db->query("select nik id from id_card where nik <= $filter[id] $where order by nik desc offset $nextOffset rows fetch next $nextLimit rows only");
                $next41 = $query->result_array();

                $next1Ids = [];
                array_walk($next41, function ($v, $k) use (&$next1Ids, $limit) {
                    if ($k % $limit == 0) {
                        $v['direction'] = 1;
                        $v['limit'] = $limit;
                        array_push($next1Ids, $v);
                    }
                });
                $nextId = count($next1Ids) > 0 ? $next1Ids[0] : null;

                $prevOffset = $limit;
                $prevLimit = $limit + 1;
                $query = $this->db->query("select nik id from id_card where nik > $filter[id] $where order by nik asc offset $prevOffset rows fetch next $prevLimit rows only");
                $prev51 = $query->result_array();

                $prev1Ids = [];
                array_walk($prev51, function ($v, $k) use (&$prev1Ids, $limit) {
                    if ($k % $limit == 0) {
                        $v['direction'] = -1;
                        $v['limit'] = $limit;
                        array_unshift($prev1Ids, $v);
                    }
                });
                $prevId = count($prev1Ids) > 0 ? $prev1Ids[count($prev1Ids) - 1] : null;
            } elseif ($filter['direction'] == -1) {
                $query = $this->db->query("select * from id_card where nik < $filter[id] $where order by nik desc offset 0 rows fetch next $limit rows only");
                $current10 = $query->result_array();

                $nextOffset = $limit;
                $nextLimit = $limit + 1;
                $query = $this->db->query("select nik id from id_card where nik < $filter[id] $where order by nik desc offset $nextOffset rows fetch next $nextLimit rows only");
                $next41 = $query->result_array();

                $next1Ids = [];
                array_walk($next41, function ($v, $k) use (&$next1Ids, $limit) {
                    if ($k % $limit == 0) {
                        $v['direction'] = 1;
                        $v['limit'] = $limit;
                        array_push($next1Ids, $v);
                    }
                });
                $nextId = count($next1Ids) > 0 ? $next1Ids[0] : null;


                $prevOffset = $limit;
                $prevLimit = $limit + 1;
                $query = $this->db->query("select nik id from id_card where nik >= $filter[id] $where order by nik asc offset $prevOffset rows fetch next $prevLimit rows only");
                $prev51 = $query->result_array();

                $prev1Ids = [];
                array_walk($prev51, function ($v, $k) use (&$prev1Ids, $limit) {
                    if ($k % $limit == 0) {
                        $v['direction'] = -1;
                        $v['limit'] = $limit;
                        array_unshift($prev1Ids, $v);
                    }
                });
                $prevId = count($prev1Ids) > 0 ? $prev1Ids[count($prev1Ids) - 1] : null;
            }
        } else if (isset($filter['id']) && $filter['id'] == 'LAST') {
            $currPage = 'LAST';
            $query = $this->db->query("select * from(select * from id_card where 1=1 $where order by nik ASC offset 0 rows fetch next $limit rows only)s order by nik desc");
            $current10 = $query->result_array();

            $prevOffset = $limit * 2;
            $prevLimit = $limit + 1;
            $query = $this->db->query("select nik id from id_card where 1=1 $where order by nik asc offset $prevOffset rows fetch next $prevLimit rows only");
            $prev11 = $query->result_array();

            $prev1Ids = [];
            array_walk($prev11, function ($v, $k) use (&$prev1Ids, $limit) {
                if ($k % $limit == 0) {
                    $v['direction'] = -1;
                    $v['limit'] = $limit;
                    array_unshift($prev1Ids, $v);
                }
            });
            $prevId = count($prev1Ids) > 0 ? $prev1Ids[count($prev1Ids) - 1] : null;
        } else {
            $currPage = 'FIRST';
            $query = $this->db->query("select * from id_card where 1=1 $where order by nik desc offset 0 rows fetch next $limit rows only");
            $current10 = $query->result_array();

            $offset = $limit;
            $nextLimit = $limit * ($PAGE_SHOW - 1) + 1;
            $query = $this->db->query("select nik id from id_card where 1=1 $where order by nik desc offset $offset rows fetch next $nextLimit rows only");
            $next41 = $query->result_array();

            $nextPage = 2;
            array_walk($next41, function ($v, $k) use (&$next5Ids, $limit, &$nextPage) {
                if ($k % $limit == 0) {
                    $v['direction'] = 1;
                    $v['page'] = $nextPage;
                    $v['limit'] = $limit;
                    array_push($next5Ids, $v);

                    $nextPage++;
                }
            });
            $nextId = count($next5Ids) > 0 ? $next5Ids[0] : null;
        }

        return [
            'currPage' => $currPage,
            'current10' => $current10,
            // 'next41'=>$next41,
            'next5Ids' => $next5Ids,
            'prev5Ids' => $prev5Ids,
            'nextId' => $nextId,
            'prevId' => $prevId,
            'where' => $where
        ];
    }

    public function find($id)
    {
        $query = $this->db->get_where($this->_table, array('id' => $id));
        return $query->row_array();
    }

    public function save($data)
    {
        //today
        $datetime = new DateTime();
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime->setTimezone($timezone);

        return $this->db->insert($this->_table, [
            'nik' => $data['nik'],
            'photo' => $data['photo'],
            'name' => strtoupper($data['name']),
            'cost_center' => strtoupper($data['cost_center']),
            'induction_date' => $data['induction_date'],
            'company_code' => $data['company_code'],
            'last_updated_at' => $datetime->format('Y-m-d H:i:s'),
        ]);
    }

    public function saveStagged($data)
    {
        return $this->db->insert_batch('id_card_stg', $data);
    }

    public function mergeStagged($uid)
    {
        return $this->db->query("exec mergeStagged '$uid'");
    }

    public function deleteStagged($uid)
    {
        return $this->db->delete('id_card_stg', array('uid' => $uid));
    }

    public function update($id, $data)
    {
        return $this->db->update($this->_table, $data, array('id' => $id));
    }

    public function destroy($id)
    {
        return $this->db->delete($this->_table, array('id' => $id));
    }
}
