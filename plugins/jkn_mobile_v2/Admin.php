<?php

namespace Plugins\JKN_Mobile_V2;

use Systems\AdminModule;
use Systems\MySQL;
use Systems\Lib\BpjsService;

class Admin extends AdminModule
{

    public function init()
    {
        $this->consid = $this->settings->get('jkn_mobile_v2.BpjsConsID');
        $this->secretkey = $this->settings->get('jkn_mobile_v2.BpjsSecretKey');
        $this->bpjsurl = $this->settings->get('jkn_mobile_v2.BpjsAntrianUrl');
        $this->user_key = $this->settings->get('jkn_mobile_v2.BpjsUserKey');
    }

    public function navigation()
    {
        return [
            'Kelola' => 'manage',
            'Index' => 'index',
            'Mapping Poliklinik' => 'mappingpoli',
            'Add Mapping Poliklinik' => 'addmappingpoli',
            'Mapping Dokter' => 'mappingdokter',
            'Add Mapping Dokter' => 'addmappingdokter',
            'Jadwal Dokter HFIS' => 'jadwaldokter',
            'Task ID' => 'taskid',
            'Dashboard Antrol BPJS' => 'antrol',
            'Pengaturan' => 'settings',
        ];
    }

    public function getManage()
    {
      $sub_modules = [
        ['name' => 'Index', 'url' => url([ADMIN, 'jkn_mobile_v2', 'index']), 'icon' => 'tasks', 'desc' => 'Index JKN Mobile V2'],
        ['name' => 'Mapping Poliklinik', 'url' => url([ADMIN, 'jkn_mobile_v2', 'mappingpoli']), 'icon' => 'tasks', 'desc' => 'Mapping Poliklinik JKN Mobile V2'],
        ['name' => 'Add Mapping Poliklinik', 'url' => url([ADMIN, 'jkn_mobile_v2', 'addmappingpoli']), 'icon' => 'tasks', 'desc' => 'Add mapping poliklinik JKN Mobile V2'],
        ['name' => 'Mapping Dokter', 'url' => url([ADMIN, 'jkn_mobile_v2', 'mappingdokter']), 'icon' => 'tasks', 'desc' => 'Mapping Dokter JKN Mobile V2'],
        ['name' => 'Add Mapping Dokter', 'url' => url([ADMIN, 'jkn_mobile_v2', 'addmappingdokter']), 'icon' => 'tasks', 'desc' => 'Add Mapping Dokter JKN Mobile V2'],
        ['name' => 'Jadwal Dokter HFIS', 'url' => url([ADMIN, 'jkn_mobile_v2', 'jadwaldokter']), 'icon' => 'tasks', 'desc' => 'Jadwal Dokter HFIS JKN Mobile V2'],
        ['name' => 'Task ID', 'url' => url([ADMIN, 'jkn_mobile_v2', 'taskid']), 'icon' => 'tasks', 'desc' => 'Task ID JKN Mobile V2'],
        ['name' => 'Dashboard Antrol BPJS', 'url' => url([ADMIN, 'jkn_mobile_v2', 'antrol']), 'icon' => 'tasks', 'desc' => 'Antrian Online BPJS'],
        ['name' => 'Pengaturan', 'url' => url([ADMIN, 'jkn_mobile_v2', 'settings']), 'icon' => 'tasks', 'desc' => 'Pengaturan JKN Mobile V2'],
      ];
      return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
    }

    public function getIndex()
    {
        return $this->draw('index.html');
    }

    public function getRefPoli()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime("1970-01-01 00:00:00"));
        $key = $this->consid.$this->secretkey.$tStamp;

        $url = $this->bpjsurl.'ref/poli';
        $output = BpjsService::get($url, NULL, $this->consid, $this->secretkey, $this->user_key, $tStamp);
        $json = json_decode($output, true);
        //echo json_encode($json);
        $code = $json['metadata']['code'];
        $message = $json['metadata']['message'];
        $stringDecrypt = stringDecrypt($key, $json['response']);
        $decompress = '""';
        if(!empty($stringDecrypt)) {
          $decompress = decompress($stringDecrypt);
        }
        if($json != null) {
          echo '{
                  "metaData": {
                      "code": "'.$code.'",
                      "message": "'.$message.'"
                  },
                  "response": '.$decompress.'}';
        } else {
          echo '{
                  "metaData": {
                      "code": "5000",
                      "message": "ERROR"
                  },
                  "response": "ADA KESALAHAN ATAU SAMBUNGAN KE SERVER BPJS TERPUTUS."}';
        }
        exit();
    }

    public function getMappingPoli()
    {
        $this->_addHeaderFiles();
        return $this->draw('mappingpoli.html', ['row' => $this->mysql('maping_poli_bpjs')->toArray()]);
    }

    public function getAddMappingPoli()
    {
        $this->_addHeaderFiles();
        $this->assign['poliklinik'] = $this->mysql('poliklinik')->where('status','1')->toArray();
        return $this->draw('form.mappingpoli.html', ['row' => $this->assign]);
    }

    public function postPoliklinik_Save()
    {

        $location = url([ADMIN, 'jkn_mobile_v2', 'addmappingpoli']);

        unset($_POST['save']);

        $query = $this->mysql('maping_poli_bpjs')->save([
            'kd_poli_rs' => $_POST['kd_poli_rs'],
            'kd_poli_bpjs' => $_POST['poli_kode'],
            'nm_poli_bpjs' => $_POST['poli_nama']
        ]);

        if ($query) {
            $this->notify('success', 'Simpan maping poli bpjs sukes');
        } else {
            $this->notify('failure', 'Simpan maping poli bpjs gagal');
        }

        redirect($location, $_POST);
    }

    public function getPoliklinik_Delete($id)
    {
        if ($this->mysql('maping_poli_bpjs')->where('kd_poli_rs', $id)->delete()) {
            $this->notify('success', 'Hapus maping poli bpjs sukses');
        } else {
            $this->notify('failure', 'Hapus maping poli bpjs gagal');
        }
        redirect(url([ADMIN, 'jkn_mobile_v2', 'mappingpoli']));
    }

    public function getRefDokter()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime("1970-01-01 00:00:00"));
        $key = $this->consid.$this->secretkey.$tStamp;

        $url = $this->bpjsurl.'ref/dokter';
        $output = BpjsService::get($url, NULL, $this->consid, $this->secretkey, $this->user_key, $tStamp);
        $json = json_decode($output, true);
        //echo json_encode($json);
        $code = $json['metadata']['code'];
        $message = $json['metadata']['message'];
        $stringDecrypt = stringDecrypt($key, $json['response']);
        $decompress = '""';
        if(!empty($stringDecrypt)) {
          $decompress = decompress($stringDecrypt);
        }
        if($json != null) {
          echo '{
                  "metaData": {
                      "code": "'.$code.'",
                      "message": "'.$message.'"
                  },
                  "response": '.$decompress.'}';
        } else {
          echo '{
                  "metaData": {
                      "code": "5000",
                      "message": "ERROR"
                  },
                  "response": "ADA KESALAHAN ATAU SAMBUNGAN KE SERVER BPJS TERPUTUS."}';
        }
        exit();
    }

    public function getMappingDokter()
    {
        $this->_addHeaderFiles();
        return $this->draw('mappingdokter.html', ['row' => $this->mysql('maping_dokter_dpjpvclaim')->toArray()]);
    }


    public function getAddMappingDokter()
    {
        $this->_addHeaderFiles();
        $this->assign['dokter'] = $this->mysql('dokter')->where('status','1')->toArray();
        return $this->draw('form.mappingdokter.html', ['row' => $this->assign]);
    }

    public function postDokter_Save()
    {

        $location = url([ADMIN, 'jkn_mobile_v2', 'addmappingdokter']);

        unset($_POST['save']);

        $query = $this->mysql('maping_dokter_dpjpvclaim')->save([
            'kd_dokter' => $_POST['kd_dokter'],
            'kd_dokter_bpjs' => $_POST['dokter_kode'],
            'nm_dokter_bpjs' => $_POST['dokter_nama']
        ]);

        if ($query) {
            $this->notify('success', 'Simpan maping poli bpjs sukes');
        } else {
            $this->notify('failure', 'Simpan maping poli bpjs gagal');
        }

        redirect($location, $_POST);
    }

    public function getDokter_Delete($id)
    {
        if ($this->mysql('maping_dokter_dpjpvclaim')->where('kd_dokter', $id)->delete()) {
            $this->notify('success', 'Hapus maping poli bpjs sukses');
        } else {
            $this->notify('failure', 'Hapus maping poli bpjs gagal');
        }
        redirect(url([ADMIN, 'jkn_mobile_v2', 'mappingdokter']));
    }

    public function getJadwalDokter()
    {
        $maping_poli_bpjs = $this->mysql('maping_poli_bpjs')->toArray();
        foreach ($maping_poli_bpjs as $value) {
          $_POST['kodepoli'] = $value['kd_poli_bpjs'];
          $kodepoli = $_POST['kodepoli'];
          $_POST['tanggal'] = date('Y-m-d');
          $tanggal = $_POST['tanggal'];
          date_default_timezone_set('UTC');
          $tStamp = strval(time() - strtotime("1970-01-01 00:00:00"));
          $key = $this->consid.$this->secretkey.$tStamp;
          date_default_timezone_set($this->settings->get('settings.timezone'));

          $url = $this->bpjsurl.'jadwaldokter/kodepoli/'.$kodepoli.'/tanggal/'.$tanggal;
          $output = BpjsService::get($url, NULL, $this->consid, $this->secretkey, $this->user_key, $tStamp);
          $json = json_decode($output, true);
          $code = $json['metadata']['code'];
          $message = $json['metadata']['message'];
          $stringDecrypt = stringDecrypt($key, $json['response']);
          $decompress = '""';
          if(!empty($stringDecrypt)) {
            $decompress = decompress($stringDecrypt);
          }
          $response = [];
          if($json['metadata']['code'] == '200') {
            $response = $decompress;
          }
        }
        //echo $response;
        $response = json_decode($response, true);
        $this->assign['list'] = $response;
        return $this->draw('jadwaldokter.html', ['row' => $this->assign]);
    }

    public function anyTaskID()
    {
      $this->_addHeaderFiles();
      $this->getCssCard();
      $date = date('Y-m-d');
      if(isset($_POST['periode_antrol']) && $_POST['periode_antrol'] !='')
        $date = $_POST['periode_antrol'];
      //$date = '2022-01-20';
      $exclude_taskid = str_replace(",","','", $this->settings->get('jkn_mobile_v2.exclude_taskid'));
      $query = $this->mysql()->pdo()->prepare("SELECT pasien.no_peserta,pasien.no_rkm_medis,pasien.no_ktp,pasien.no_tlp,reg_periksa.no_reg,reg_periksa.no_rawat,reg_periksa.tgl_registrasi,reg_periksa.kd_dokter,dokter.nm_dokter,reg_periksa.kd_poli,poliklinik.nm_poli,reg_periksa.stts_daftar,reg_periksa.no_rkm_medis
      FROM reg_periksa INNER JOIN pasien ON reg_periksa.no_rkm_medis=pasien.no_rkm_medis INNER JOIN dokter ON reg_periksa.kd_dokter=dokter.kd_dokter INNER JOIN poliklinik ON reg_periksa.kd_poli=poliklinik.kd_poli WHERE reg_periksa.tgl_registrasi='$date' AND reg_periksa.kd_poli NOT IN ('$exclude_taskid')
      ORDER BY concat(reg_periksa.tgl_registrasi,' ',reg_periksa.jam_reg)");
      $query->execute();
      $query = $query->fetchAll(\PDO::FETCH_ASSOC);;

      $rows = [];
      foreach ($query as $q) {
          $reg_periksa = $this->mysql('reg_periksa')->where('tgl_registrasi', $date)->where('no_rkm_medis', $q['no_rkm_medis'])->where('stts', '<>', 'Batal')->oneArray();
          $reg_periksa2 = $this->mysql('reg_periksa')->where('tgl_registrasi', $date)->where('no_rkm_medis', $q['no_rkm_medis'])->where('stts', 'Batal')->oneArray();
          $batal = '0000-00-00 00:00:00';
          if($reg_periksa2) {
            $batal = $q['tgl_registrasi'].' '.date('H:i:s');
          }
          $mlite_antrian_referensi = $this->mysql('mlite_antrian_referensi')->where('tanggal_periksa', $q['tgl_registrasi'])->where('nomor_kartu', $q['no_peserta'])->oneArray();
          if(!$mlite_antrian_referensi) {
              $mlite_antrian_referensi = $this->mysql('mlite_antrian_referensi')->where('tanggal_periksa', $q['tgl_registrasi'])->where('nomor_kartu', $q['no_rkm_medis'])->oneArray();
          }
          $mutasi_berkas = $this->mysql('mutasi_berkas')->select('dikirim')->where('no_rawat', $reg_periksa['no_rawat'])->where('dikirim', '<>', '0000-00-00 00:00:00')->oneArray();
          $mutasi_berkas2 = $this->mysql('mutasi_berkas')->select('diterima')->where('no_rawat', $reg_periksa['no_rawat'])->where('diterima', '<>', '0000-00-00 00:00:00')->oneArray();
          $pemeriksaan_ralan = $this->mysql('pemeriksaan_ralan')->select(['datajam' => 'concat(tgl_perawatan," ",jam_rawat)'])->where('no_rawat', $reg_periksa['no_rawat'])->oneArray();
          $resep_obat = $this->mysql('resep_obat')->select(['datajam' => 'concat(tgl_perawatan," ",jam)'])->where('no_rawat', $reg_periksa['no_rawat'])->oneArray();
          $resep_obat2 = $this->mysql('resep_obat')->select(['datajam' => 'concat(tgl_peresepan," ",jam_peresepan)'])->where('no_rawat', $reg_periksa['no_rawat'])->where('concat(tgl_perawatan," ",jam)', '<>', 'concat(tgl_peresepan," ",jam_peresepan)')->oneArray();

          $mlite_antrian_loket = $this->mysql('mlite_antrian_loket')->where('postdate', $date)->where('no_rkm_medis', $q['no_rkm_medis'])->oneArray();
          $task1 = '';
          $task2 = '';
          if($mlite_antrian_loket) {
            $task1 = $mlite_antrian_loket['postdate'].' '.$mlite_antrian_loket['start_time'];
            $task2 = $mlite_antrian_loket['postdate'].' '.$mlite_antrian_loket['end_time'];
          }
          $q['nomor_referensi'] = $mlite_antrian_referensi['nomor_referensi'];
          /*$q['task1'] = strtotime($task1) * 1000;
          $q['task2'] = strtotime($task2) * 1000;
          $q['task3'] = strtotime($mutasi_berkas['dikirim']) * 1000;
          $q['task4'] = strtotime($mutasi_berkas2['diterima']) * 1000;
          $q['task5'] = strtotime($pemeriksaan_ralan['datajam']) * 1000;
          $q['task6'] = strtotime($resep_obat['datajam']) * 1000;
          $q['task7'] = strtotime($resep_obat2['datajam']) * 1000;
          $q['task99'] = $batal;*/
          $q['task1'] = $task1;
          $q['task2'] = $task2;
          $q['task3'] = $mutasi_berkas['dikirim'];
          $q['task4'] = $mutasi_berkas2['diterima'];
          $q['task5'] = $pemeriksaan_ralan['datajam'];
          $q['task6'] = $resep_obat2['datajam'];
          $q['task7'] = $resep_obat['datajam'];
          $q['task99'] = $batal;
          $rows[] = $q;
      }

      $taskid = $rows;
      return $this->draw('taskid.html', ['taskid' => $taskid]);
    }

    public function getSettings()
    {
        $this->_addHeaderFiles();
        $this->assign['title'] = 'Pengaturan Modul JKN Mobile';
        $this->assign['propinsi'] = $this->mysql('propinsi')->where('kd_prop', $this->settings->get('jkn_mobile_v2.kdprop'))->oneArray();
        $this->assign['kabupaten'] = $this->mysql('kabupaten')->where('kd_kab', $this->settings->get('jkn_mobile_v2.kdkab'))->oneArray();
        $this->assign['kecamatan'] = $this->mysql('kecamatan')->where('kd_kec', $this->settings->get('jkn_mobile_v2.kdkec'))->oneArray();
        $this->assign['kelurahan'] = $this->mysql('kelurahan')->where('kd_kel', $this->settings->get('jkn_mobile_v2.kdkel'))->oneArray();
        $this->assign['suku_bangsa'] = $this->mysql('suku_bangsa')->toArray();
        $this->assign['bahasa_pasien'] = $this->mysql('bahasa_pasien')->toArray();
        $this->assign['cacat_fisik'] = $this->mysql('cacat_fisik')->toArray();
        $this->assign['perusahaan_pasien'] = $this->mysql('perusahaan_pasien')->toArray();
        $this->assign['poliklinik'] = $this->_getPoliklinik($this->settings->get('jkn_mobile_v2.display'));
        $this->assign['exclude_taskid'] = $this->_getPoliklinik($this->settings->get('jkn_mobile_v2.exclude_taskid'));
        $this->assign['penjab'] = $this->mysql('penjab')->toArray();

        $this->assign['jkn_mobile_v2'] = htmlspecialchars_array($this->settings('jkn_mobile_v2'));
        return $this->draw('settings.html', ['settings' => $this->assign]);
    }

    public function postSaveSettings()
    {
        $_POST['jkn_mobile_v2']['display'] = implode(',', $_POST['jkn_mobile_v2']['display']);
        $_POST['jkn_mobile_v2']['exclude_taskid'] = implode(',', $_POST['jkn_mobile_v2']['exclude_taskid']);
        foreach ($_POST['jkn_mobile_v2'] as $key => $val) {
            $this->settings('jkn_mobile_v2', $key, $val);
        }
        $this->notify('success', 'Pengaturan telah disimpan');
        redirect(url([ADMIN, 'jkn_mobile_v2', 'settings']));
    }

    private function _getPoliklinik($kd_poli = null)
    {
        $result = [];
        $rows = $this->mysql('poliklinik')->toArray();

        if (!$kd_poli) {
            $kd_poliArray = [];
        } else {
            $kd_poliArray = explode(',', $kd_poli);
        }

        foreach ($rows as $row) {
            if (empty($kd_poliArray)) {
                $attr = '';
            } else {
                if (in_array($row['kd_poli'], $kd_poliArray)) {
                    $attr = 'selected';
                } else {
                    $attr = '';
                }
            }
            $result[] = ['kd_poli' => $row['kd_poli'], 'nm_poli' => $row['nm_poli'], 'attr' => $attr];
        }
        return $result;
    }

    public function anyAntrol()
    {
        $this->getCssCard();
        $tgl_kunjungan = date('Y-m-d');
        $bulan = substr($tgl_kunjungan, 5, 2);
        $tahun = substr($tgl_kunjungan, 0, 4);
        $tanggal = substr($tgl_kunjungan, 8, 2);
        $depanUrlTanggal = $this->bpjsurl . 'dashboard/waktutunggu/tanggal/';
        $depanUrlBulan = $this->bpjsurl . 'dashboard/waktutunggu/bulan/';
        if (isset($_POST['periode'])) {
            $waktu = $_POST['waktu'];
            $tgl_kunjungan = $_POST['periode'];
            $tgl_kunjungan = preg_replace('/\s+/', '', $tgl_kunjungan);
            $bulan = substr($tgl_kunjungan, 5, 2);
            $tahun = substr($tgl_kunjungan, 0, 4);
            $tanggal = substr($tgl_kunjungan, 8, 2);
            if ($_POST['rute'] == 'tanggal') {
                $url = $depanUrlTanggal . $tahun . '-' . $bulan . '-' . $tanggal . '/waktu/' . $waktu;
            } else {
                $url = $depanUrlBulan . $bulan . '/tahun/' . $tahun . '/waktu/' . $waktu;
            }
            $output = BpjsService::get($url, NULL, $this->consid, $this->secretkey, $this->user_key, NULL);
            $json = json_decode($output, true);
            $response = [];
            if($json['metadata']['code'] == '200') {
              $response = $json['response']['list'];
            }
            $this->assign['list'] = $response;

            echo $this->draw('antrol.display.html', ['row' => $this->assign]);
        } else {
            $url = $depanUrlTanggal . $tahun . '-' . $bulan . '-' . $tanggal . '/waktu/server';
            $output = BpjsService::get($url, NULL, $this->consid, $this->secretkey, $this->user_key, NULL);
            $json = json_decode($output, true);
            $response = [];
            if($json['metadata']['code'] == '200') {
              $response = $json['response']['list'];
            }
            $this->assign['list'] = $response;

            return $this->draw('antrol.html', ['row' => $this->assign]);
        }
        exit();
    }

    public function getAjax()
    {
        header('Content-type: text/html');
        $show = isset($_GET['show']) ? $_GET['show'] : "";
        switch($show){
        	default:
          break;
        	case "propinsi":
          $propinsi = $this->mysql('propinsi')->toArray();
          foreach ($propinsi as $row) {
            echo '<tr class="pilihpropinsi" data-kdprop="'.$row['kd_prop'].'" data-namaprop="'.$row['nm_prop'].'">';
      			echo '<td>'.$row['kd_prop'].'</td>';
      			echo '<td>'.$row['nm_prop'].'</td>';
      			echo '</tr>';
          }
          break;
          case "kabupaten":
          $kabupaten = $this->mysql('kabupaten')->toArray();
          foreach ($kabupaten as $row) {
            echo '<tr class="pilihkabupaten" data-kdkab="'.$row['kd_kab'].'" data-namakab="'.$row['nm_kab'].'">';
      			echo '<td>'.$row['kd_kab'].'</td>';
      			echo '<td>'.$row['nm_kab'].'</td>';
      			echo '</tr>';
          }
          break;
          case "kecamatan":
          $kecamatan = $this->mysql('kecamatan')->toArray();
          foreach ($kecamatan as $row) {
            echo '<tr class="pilihkecamatan" data-kdkec="'.$row['kd_kec'].'" data-namakec="'.$row['nm_kec'].'">';
      			echo '<td>'.$row['kd_kec'].'</td>';
      			echo '<td>'.$row['nm_kec'].'</td>';
      			echo '</tr>';
          }
          break;
          case "kelurahan":
          // Alternative SQL join in Datatables
          $id_table = 'kd_kel';
          $columns = array(
                       'kd_kel',
                       'nm_kel'
                     );
          //$action = '"Test" as action';
          // gunakan join disini
          $from = 'kelurahan';

          $id_table = $id_table != '' ? $id_table . ',' : '';
          // custom SQL
          $sql = "SELECT {$id_table} ".implode(',', $columns)." FROM {$from}";

          // search
          if (isset($_GET['search']['value']) && $_GET['search']['value'] != '') {
              $search = $_GET['search']['value'];
              $where  = '';
              // create parameter pencarian kesemua kolom yang tertulis
              // di $columns
              for ($i=0; $i < count($columns); $i++) {
                  $where .= $columns[$i] . ' LIKE "%'.$search.'%"';

                  // agar tidak menambahkan 'OR' diakhir Looping
                  if ($i < count($columns)-1) {
                      $where .= ' OR ';
                  }
              }

              $sql .= ' WHERE ' . $where;
          }

          //SORT Kolom
          $sortColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 0;
          $sortDir    = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';

          $sortColumn = $columns[$sortColumn];

          $sql .= " ORDER BY {$sortColumn} {$sortDir}";

          $query = $this->mysql()->pdo()->prepare($sql);
          $query->execute();
          $query = $query->fetchAll();

          // var_dump($sql);
          //$count = $database->query($sql);
          // hitung semua data
          $totaldata = count($query);

          // memberi Limit
          $start  = isset($_GET['start']) ? $_GET['start'] : 0;
          $length = isset($_GET['length']) ? $_GET['length'] : 10;


          $sql .= " LIMIT {$start}, {$length}";

          $data = $this->mysql()->pdo()->prepare($sql);
          $data->execute();
          $data = $data->fetchAll();

          // create json format
          $datatable['draw']            = isset($_GET['draw']) ? $_GET['draw'] : 1;
          $datatable['recordsTotal']    = $totaldata;
          $datatable['recordsFiltered'] = $totaldata;
          $datatable['data']            = array();

          foreach ($data as $row) {

              $fields = array();
              $fields['0'] = $row['kd_kel'];
              $fields['1'] = '<span class="pilihkelurahan" data-kdkel="'.$row['kd_kel'].'" data-namakel="'.$row['nm_kel'].'">'.$row['nm_kel'].'</span>';
              $datatable['data'][] = $fields;

          }

          echo json_encode($datatable);

          break;

        }
        exit();
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES.'/jkn_mobile_v2/js/admin/jkn_mobile_v2.js');
        exit();
    }

    public function getCssCard()
    {
        $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));
        $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
        $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));
    }

    private function _addHeaderFiles()
    {
        // CSS
        $this->core->addCSS(url('assets/css/jquery-ui.css'));
        $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));

        // JS
        $this->core->addJS(url('assets/jscripts/jquery-ui.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'), 'footer');

        // MODULE SCRIPTS
        $this->core->addJS(url([ADMIN, 'jkn_mobile_v2', 'javascript']), 'footer');
    }

    protected function mysql($table = NULL)
    {
        return new MySQL($table);
    }

}
