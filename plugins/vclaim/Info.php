<?php

return [
    'name'          =>  'VClaim Request',
    'description'   =>  'Modul vclaim api untuk KhanzaLITE',
    'author'        =>  'Basoro',
    'version'       =>  '1.1',
    'compatibility' =>  '2022',
    'icon'          =>  'database',
    'install'       =>  function () use ($core) {

      $core->mysql()->pdo()->exec("CREATE TABLE IF NOT EXISTS `bridging_sep` (
        `no_sep` varchar(40) NOT NULL DEFAULT '',
        `no_rawat` varchar(17) DEFAULT NULL,
        `tglsep` date DEFAULT NULL,
        `tglrujukan` date DEFAULT NULL,
        `no_rujukan` varchar(40) DEFAULT NULL,
        `kdppkrujukan` varchar(12) DEFAULT NULL,
        `nmppkrujukan` varchar(200) DEFAULT NULL,
        `kdppkpelayanan` varchar(12) DEFAULT NULL,
        `nmppkpelayanan` varchar(200) DEFAULT NULL,
        `jnspelayanan` enum('1','2') DEFAULT NULL,
        `catatan` varchar(100) DEFAULT NULL,
        `diagawal` varchar(10) DEFAULT NULL,
        `nmdiagnosaawal` varchar(400) DEFAULT NULL,
        `kdpolitujuan` varchar(15) DEFAULT NULL,
        `nmpolitujuan` varchar(50) DEFAULT NULL,
        `klsrawat` enum('1','2','3') DEFAULT NULL,
        `klsnaik` enum('','1','2') NOT NULL,
        `pembiayaan` enum('','1','2','3') NOT NULL,
        `pjnaikkelas` varchar(100) NOT NULL,
        `lakalantas` enum('0','1') DEFAULT NULL,
        `user` varchar(25) DEFAULT NULL,
        `nomr` varchar(15) DEFAULT NULL,
        `nama_pasien` varchar(100) DEFAULT NULL,
        `tanggal_lahir` date DEFAULT NULL,
        `peserta` varchar(100) DEFAULT NULL,
        `jkel` enum('L','P') DEFAULT NULL,
        `no_kartu` varchar(25) DEFAULT NULL,
        `tglpulang` datetime DEFAULT NULL,
        `asal_rujukan` enum('1. Faskes 1','2. Faskes 2(RS)') NOT NULL,
        `eksekutif` enum('0. Tidak','1.Ya') NOT NULL,
        `cob` enum('0. Tidak','1.Ya') NOT NULL,
        `notelep` varchar(40) NOT NULL,
        `katarak` enum('0. Tidak','1.Ya') NOT NULL,
        `tglkkl` date NOT NULL,
        `keterangankkl` varchar(100) NOT NULL,
        `suplesi` enum('0. Tidak','1.Ya') NOT NULL,
        `no_sep_suplesi` varchar(40) NOT NULL,
        `kdprop` varchar(10) NOT NULL,
        `nmprop` varchar(50) NOT NULL,
        `kdkab` varchar(10) NOT NULL,
        `nmkab` varchar(50) NOT NULL,
        `kdkec` varchar(10) NOT NULL,
        `nmkec` varchar(50) NOT NULL,
        `noskdp` varchar(40) NOT NULL,
        `kddpjp` varchar(10) NOT NULL,
        `nmdpdjp` varchar(100) NOT NULL,
        `tujuankunjungan` enum('0','1','2') NOT NULL,
        `flagprosedur` enum('','0','1') NOT NULL,
        `penunjang` enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL,
        `asesmenpelayanan` enum('','1','2','3','4') NOT NULL,
        `kddpjplayanan` varchar(10) NOT NULL,
        `nmdpjplayanan` varchar(100) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

      $core->mysql()->pdo()->exec("ALTER TABLE `bridging_sep`
        ADD PRIMARY KEY (`no_sep`),
        ADD KEY `no_rawat` (`no_rawat`);");

      $core->mysql()->pdo()->exec("ALTER TABLE `bridging_sep`
        ADD CONSTRAINT `bridging_sep_ibfk_1` FOREIGN KEY (`no_rawat`) REFERENCES `reg_periksa` (`no_rawat`) ON DELETE CASCADE ON UPDATE CASCADE;");

      $core->mysql()->pdo()->exec("CREATE TABLE IF NOT EXISTS `bridging_sep_internal` (
        `no_sep` varchar(40) NOT NULL DEFAULT '',
        `no_rawat` varchar(17) DEFAULT NULL,
        `tglsep` date DEFAULT NULL,
        `tglrujukan` date DEFAULT NULL,
        `no_rujukan` varchar(40) DEFAULT NULL,
        `kdppkrujukan` varchar(12) DEFAULT NULL,
        `nmppkrujukan` varchar(200) DEFAULT NULL,
        `kdppkpelayanan` varchar(12) DEFAULT NULL,
        `nmppkpelayanan` varchar(200) DEFAULT NULL,
        `jnspelayanan` enum('1','2') DEFAULT NULL,
        `catatan` varchar(100) DEFAULT NULL,
        `diagawal` varchar(10) DEFAULT NULL,
        `nmdiagnosaawal` varchar(400) DEFAULT NULL,
        `kdpolitujuan` varchar(15) DEFAULT NULL,
        `nmpolitujuan` varchar(50) DEFAULT NULL,
        `klsrawat` enum('1','2','3') DEFAULT NULL,
        `klsnaik` enum('','1','2') NOT NULL,
        `pembiayaan` enum('','1','2','3') NOT NULL,
        `pjnaikkelas` varchar(100) NOT NULL,
        `lakalantas` enum('0','1') DEFAULT NULL,
        `user` varchar(25) DEFAULT NULL,
        `nomr` varchar(15) DEFAULT NULL,
        `nama_pasien` varchar(100) DEFAULT NULL,
        `tanggal_lahir` date DEFAULT NULL,
        `peserta` varchar(100) DEFAULT NULL,
        `jkel` enum('L','P') DEFAULT NULL,
        `no_kartu` varchar(25) DEFAULT NULL,
        `tglpulang` datetime DEFAULT NULL,
        `asal_rujukan` enum('1. Faskes 1','2. Faskes 2(RS)') NOT NULL,
        `eksekutif` enum('0. Tidak','1.Ya') NOT NULL,
        `cob` enum('0. Tidak','1.Ya') NOT NULL,
        `notelep` varchar(40) NOT NULL,
        `katarak` enum('0. Tidak','1.Ya') NOT NULL,
        `tglkkl` date NOT NULL,
        `keterangankkl` varchar(100) NOT NULL,
        `suplesi` enum('0. Tidak','1.Ya') NOT NULL,
        `no_sep_suplesi` varchar(40) NOT NULL,
        `kdprop` varchar(10) NOT NULL,
        `nmprop` varchar(50) NOT NULL,
        `kdkab` varchar(10) NOT NULL,
        `nmkab` varchar(50) NOT NULL,
        `kdkec` varchar(10) NOT NULL,
        `nmkec` varchar(50) NOT NULL,
        `noskdp` varchar(40) NOT NULL,
        `kddpjp` varchar(10) NOT NULL,
        `nmdpdjp` varchar(100) NOT NULL,
        `tujuankunjungan` enum('0','1','2') NOT NULL,
        `flagprosedur` enum('','0','1') NOT NULL,
        `penunjang` enum('','1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL,
        `asesmenpelayanan` enum('','1','2','3','4') NOT NULL,
        `kddpjplayanan` varchar(10) NOT NULL,
        `nmdpjplayanan` varchar(100) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

      $core->mysql()->pdo()->exec("ALTER TABLE `bridging_sep_internal`
        ADD KEY `no_rawat` (`no_rawat`),
        ADD KEY `no_sep` (`no_sep`);");

      $core->mysql()->pdo()->exec("ALTER TABLE `bridging_sep_internal`
        ADD CONSTRAINT `bridging_sep_internal_ibfk_1` FOREIGN KEY (`no_rawat`) REFERENCES `reg_periksa` (`no_rawat`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `bridging_sep_internal_ibfk_2` FOREIGN KEY (`no_sep`) REFERENCES `bridging_sep` (`no_sep`) ON DELETE CASCADE ON UPDATE CASCADE;");

      $core->mysql()->pdo()->exec("CREATE TABLE IF NOT EXISTS `bpjs_prb` (
        `no_sep` varchar(40) NOT NULL,
        `prb` varchar(50) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

      $core->mysql()->pdo()->exec("ALTER TABLE `bpjs_prb`
        ADD PRIMARY KEY (`no_sep`);");

      $core->mysql()->pdo()->exec("ALTER TABLE `bpjs_prb`
        ADD CONSTRAINT `bpjs_prb_ibfk_1` FOREIGN KEY (`no_sep`) REFERENCES `bridging_sep` (`no_sep`) ON DELETE CASCADE ON UPDATE CASCADE;");

      $core->mysql()->pdo()->exec("CREATE TABLE IF NOT EXISTS `bridging_surat_kontrol_bpjs` (
        `no_sep` varchar(40) DEFAULT NULL,
        `tgl_surat` date NOT NULL,
        `no_surat` varchar(40) NOT NULL,
        `tgl_rencana` date DEFAULT NULL,
        `kd_dokter_bpjs` varchar(20) DEFAULT NULL,
        `nm_dokter_bpjs` varchar(50) DEFAULT NULL,
        `kd_poli_bpjs` varchar(15) DEFAULT NULL,
        `nm_poli_bpjs` varchar(40) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

      $core->mysql()->pdo()->exec("ALTER TABLE `bridging_surat_kontrol_bpjs`
        ADD PRIMARY KEY (`no_surat`),
        ADD KEY `bridging_surat_kontrol_bpjs_ibfk_1` (`no_sep`);");

      $core->mysql()->pdo()->exec("ALTER TABLE `bridging_surat_kontrol_bpjs`
        ADD CONSTRAINT `bridging_surat_kontrol_bpjs_ibfk_1` FOREIGN KEY (`no_sep`) REFERENCES `bridging_sep` (`no_sep`) ON DELETE CASCADE ON UPDATE CASCADE;");

      $core->mysql()->pdo()->exec("CREATE TABLE IF NOT EXISTS `bridging_surat_pri_bpjs` (
        `no_rawat` varchar(17) DEFAULT NULL,
        `no_kartu` varchar(25) DEFAULT NULL,
        `tgl_surat` date NOT NULL,
        `no_surat` varchar(40) NOT NULL,
        `tgl_rencana` date DEFAULT NULL,
        `kd_dokter_bpjs` varchar(20) DEFAULT NULL,
        `nm_dokter_bpjs` varchar(50) DEFAULT NULL,
        `kd_poli_bpjs` varchar(15) DEFAULT NULL,
        `nm_poli_bpjs` varchar(40) DEFAULT NULL,
        `diagnosa` varchar(130) NOT NULL,
        `no_sep` varchar(40) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

      $core->mysql()->pdo()->exec("ALTER TABLE `bridging_surat_pri_bpjs`
        ADD PRIMARY KEY (`no_surat`),
        ADD KEY `no_rawat` (`no_rawat`);");

      $core->mysql()->pdo()->exec("ALTER TABLE `bridging_surat_pri_bpjs`
        ADD CONSTRAINT `bridging_surat_pri_bpjs_ibfk_1` FOREIGN KEY (`no_rawat`) REFERENCES `reg_periksa` (`no_rawat`) ON DELETE CASCADE ON UPDATE CASCADE;");

      $core->mysql()->pdo()->exec("CREATE TABLE IF NOT EXISTS `maping_dokter_dpjpvclaim` (
        `kd_dokter` varchar(20) NOT NULL,
        `kd_dokter_bpjs` varchar(20) DEFAULT NULL,
        `nm_dokter_bpjs` varchar(50) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;");

      $core->mysql()->pdo()->exec("ALTER TABLE `maping_dokter_dpjpvclaim`
        ADD PRIMARY KEY (`kd_dokter`) USING BTREE;");

      $core->mysql()->pdo()->exec("ALTER TABLE `maping_dokter_dpjpvclaim`
        ADD CONSTRAINT `maping_dokter_dpjpvclaim_ibfk_1` FOREIGN KEY (`kd_dokter`) REFERENCES `dokter` (`kd_dokter`) ON DELETE CASCADE ON UPDATE CASCADE;");

      $core->mysql()->pdo()->exec("CREATE TABLE IF NOT EXISTS `maping_poli_bpjs` (
        `kd_poli_rs` varchar(5) NOT NULL,
        `kd_poli_bpjs` varchar(15) NOT NULL,
        `nm_poli_bpjs` varchar(40) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

      $core->mysql()->pdo()->exec("ALTER TABLE `maping_poli_bpjs`
        ADD PRIMARY KEY (`kd_poli_rs`);");

      $core->mysql()->pdo()->exec("ALTER TABLE `maping_poli_bpjs`
        ADD CONSTRAINT `maping_poli_bpjs_ibfk_1` FOREIGN KEY (`kd_poli_rs`) REFERENCES `poliklinik` (`kd_poli`) ON DELETE CASCADE ON UPDATE CASCADE;");

      if (!is_dir(UPLOADS."/qrcode")) {
          mkdir(UPLOADS."/qrcode", 0777);
      }
      if (!is_dir(UPLOADS."/qrcode/sep")) {
          mkdir(UPLOADS."/qrcode/sep", 0777);
      }

    },
    'uninstall'     =>  function() use($core)
    {
    }
];
