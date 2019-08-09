-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2018 at 09:16 PM
-- Server version: 5.5.32
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cmsid_2016`
--
CREATE DATABASE IF NOT EXISTS `cmsid_2016` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `cmsid_2016`;

-- --------------------------------------------------------

--
-- Table structure for table `cmsid_category`
--

CREATE TABLE IF NOT EXISTS `cmsid_category` (
  `category_ID` int(12) NOT NULL AUTO_INCREMENT,
  `category_group` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_desc` text NOT NULL,
  PRIMARY KEY (`category_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cmsid_category`
--

INSERT INTO `cmsid_category` (`category_ID`, `category_group`, `category_name`, `category_desc`) VALUES
(1, 0, 'Sebuah kategori', '0');

-- --------------------------------------------------------

--
-- Table structure for table `cmsid_category_relations`
--

CREATE TABLE IF NOT EXISTS `cmsid_category_relations` (
  `relations_ID` int(11) NOT NULL AUTO_INCREMENT,
  `relations_category_ID` int(11) NOT NULL,
  `relations_post_ID` int(11) NOT NULL,
  PRIMARY KEY (`relations_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `cmsid_category_relations`
--

INSERT INTO `cmsid_category_relations` (`relations_ID`, `relations_category_ID`, `relations_post_ID`) VALUES
(1, 1, 1),
(2, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `cmsid_comments`
--

CREATE TABLE IF NOT EXISTS `cmsid_comments` (
  `comment_id` int(12) NOT NULL AUTO_INCREMENT,
  `comment_parent` int(11) NOT NULL,
  `comment_title` varchar(100) NOT NULL,
  `comment_content` text NOT NULL,
  `comment_date` date NOT NULL,
  `comment_author` varchar(30) NOT NULL,
  `comment_author_email` varchar(60) NOT NULL,
  `comment_author_url` varchar(80) NOT NULL,
  `comment_post_ID` int(11) NOT NULL,
  `comment_approve` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `cmsid_comments`
--

INSERT INTO `cmsid_comments` (`comment_id`, `comment_parent`, `comment_title`, `comment_content`, `comment_date`, `comment_author`, `comment_author_email`, `comment_author_url`, `comment_post_ID`, `comment_approve`) VALUES
(2, 0, 'Halo Semua', 'Hai, ini adalah komentar.<br />\\r\\nUntuk menghapus sebuah komentar, cukup masuk log dan lihat komentar tulisan tersebut. Di sana Anda akan memiliki pilihan untuk mengedit atau menghapusnya.', '0000-00-00', 'Eko', 'eko.hendratno@gmail.com', 'http://cmsid.org', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cmsid_options`
--

CREATE TABLE IF NOT EXISTS `cmsid_options` (
  `option_ID` int(11) NOT NULL AUTO_INCREMENT,
  `option_name` varchar(68) NOT NULL,
  `option_value` longtext NOT NULL,
  PRIMARY KEY (`option_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

--
-- Dumping data for table `cmsid_options`
--

INSERT INTO `cmsid_options` (`option_ID`, `option_name`, `option_value`) VALUES
(1, 'account_registration', '1'),
(2, 'active_plugins', '{"posts/posts.php":"1"}'),
(3, 'admin_email', 'eko.hendratno@gmail.com'),
(4, 'author', 'Eko Azza'),
(5, 'avatar_default', 'mystery'),
(6, 'avatar_type', 'computer'),
(7, 'background_admin', ''),
(8, 'body_layout', 'left'),
(9, 'dashboard_widget', '{"normal":"dashboard_feed_news,dashboard_quick_post,","side":"dashboard_recent_registration,"}'),
(10, 'datetime_format', 'Y/m/d'),
(11, 'date_format', 'F j, Y'),
(12, 'feed-news', '{"news_feeds":{"News Feed cmsid.org":"http://cmsid.org/rss.xml"},"display":{"desc":1,"author":1,"date":1,"limit":30}}'),
(13, 'file_allaw', '["txt","csv","htm","html","xml","css","doc","xls","rtf","ppt","pdf","swf","flv","avi","wmv","mov","jpg","jpeg","gif","png"]'),
(14, 'frame', '0'),
(15, 'help_guide', ''),
(16, 'html_type', 'text/html'),
(17, 'id', ''),
(18, 'image_allaw', '{"image\\/png":".png","image\\/x-png":".png","image\\/gif":".gif","image\\/jpeg":".jpg","image\\/pjpeg":".jpg"}'),
(19, 'menu-action', '[''aksi'':{''posts'':{''title'':''Post'',''link'':''?action=post''},''pages'':{''title'':''Pages'',''link'':''?action=pages''}}]'),
(20, 'post_comment', '1'),
(21, 'post_comment_filter', '1'),
(22, 'post_limit', '10'),
(23, 'rewrite', 'advance'),
(24, 'rewrite_html', ''),
(25, 'robots', ''),
(26, 'security_pip', ''),
(27, 'sidebar_actions', ''),
(28, 'sidebar_widgets', '{"sidebar-1":["pages","meta","categories","archives"],"sidebar-2":["pages","archives"]}'),
(29, 'sitedescription', 'Keterangan dari website'),
(30, 'sitekeywords', 'keyword website'),
(31, 'sitename', '2016 Alpha'),
(32, 'siteslogan', 'slogan website'),
(33, 'siteurl', 'http://localhost/cmsid/build-n/2016'),
(34, 'site_charset', 'UTF-8'),
(35, 'site_copyright', '2012 | CMS ID'),
(36, 'site_public', '1'),
(37, 'template', 'startbootstrap'),
(38, 'timezone', 'Asia/Jakarta'),
(39, 'toogle_menuaction', ''),
(40, 'toogle_menutop', ''),
(41, 'use_smilies', '1'),
(42, 'welcome', '');

-- --------------------------------------------------------

--
-- Table structure for table `cmsid_posts`
--

CREATE TABLE IF NOT EXISTS `cmsid_posts` (
  `post_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_user_ID` int(11) NOT NULL,
  `post_date` datetime NOT NULL,
  `post_title` text NOT NULL,
  `post_content` longtext NOT NULL,
  `post_hits` int(11) NOT NULL,
  `post_title_self` varchar(200) NOT NULL,
  `post_type` varchar(20) NOT NULL,
  `post_format` enum('standar','image','video','quote','link','gallery','audio') NOT NULL DEFAULT 'standar',
  `post_status` int(1) NOT NULL DEFAULT '0',
  `comment_status` enum('open','draf','disable') NOT NULL DEFAULT 'open',
  `post_thumb` longtext NOT NULL,
  `post_thumb_desc` text NOT NULL,
  `post_approved` int(1) NOT NULL DEFAULT '0',
  `post_meta_keys` text NOT NULL,
  `post_meta_desc` text NOT NULL,
  `post_headline` int(1) NOT NULL,
  `post_parent` int(11) NOT NULL,
  PRIMARY KEY (`post_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `cmsid_posts`
--

INSERT INTO `cmsid_posts` (`post_ID`, `post_user_ID`, `post_date`, `post_title`, `post_content`, `post_hits`, `post_title_self`, `post_type`, `post_format`, `post_status`, `comment_status`, `post_thumb`, `post_thumb_desc`, `post_approved`, `post_meta_keys`, `post_meta_desc`, `post_headline`, `post_parent`) VALUES
(1, 1, '2013-03-22 14:11:29', 'Hallo Semua!', '<p>Selamat datang di CMS ID. Ini adalah tulisan pertama Anda. Sunting atau hapus, kemudian mulai membuat artikel!<span id="pastemarkerend"></span><br>\r\n\r\n</p>\r\n', 18, 'hallo-semua', 'post', 'standar', 1, 'open', '', '', 1, '', '', 0, 0),
(2, 1, '2000-07-19 00:00:00', 'Sample Page', '<p>Ini adalah contoh halaman. Yang berbeda dari tulisan karena\r\nakan menjadi satu kesatuan dan akan tampil pada menu navigasi situs (tema). Kebanyakan\r\norang memulai halamannya dengan menuliskan tentang mereka kenalkan ke\r\npengunjung situs. Kata katanya mungkin seperti ini:</p>\r\n\r\n\r\n<blockquote>Hi semua! Saya memiliki pesan hari ini, ini adalah situs\r\nsaya. Saya tinggal di Bandar Lampung, Indonesia, memiliki keluarga yang sangat\r\nhebat, memiliki kucing bernama Miaw, dan saya suka sekali dengan permainan bulu\r\ntangkis dan bola voli</blockquote>\r\n\r\nAtau bisa seperti ini:<br>\r\n\r\n\r\n<blockquote>Perusahaan tanpa nama XYZ didirikan pada tahun 1971, dan\r\ntelah menyediakan jasa informasi berkualitas kepada publik sampai saat ini. Terletak\r\ndi Kota Jakarta, XYZ memperkerjakan lebih dari 10000 karyawan dan melakukkan\r\nsegala macam hal yang mengagumkan bagi masyarakat sekitar.</blockquote>\r\n\r\n<p>Sebagai pengguna&nbsp;<a href="http://cmsid.org/">cmsid</a>&nbsp;yang baru, Anda harus pergi ke\r\ndashboard posting artikel untuk menghapus halaman ini dan mulai membuat halaman\r\nbaru untuk konten Anda. Have fun!. </p>\r\n', 0, 'sample-page', 'page', 'standar', 1, 'open', '', '', 1, '', '', 1, 0),
(3, 1, '2013-03-24 13:01:28', 'Support Us', '<p>Kelangsungan ketersediaan widget dan layanan web situs&nbsp;ini tergantung kepada bantuan dan dukungan dari anda. Banyak cara untuk mewujudkan dukungan tersebut.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Beritahu Yang Lain!</strong></p>\r\n<p>Silakan gunakan satu atau dua (atau lebih)&nbsp;pada situs atau blog yang anda punyai. Jika anda menggunakan&nbsp;twitter,&nbsp;atau tweet-ulang tulisan-tulisan kami. Jika anda adalah penggemar&nbsp;facebook, jadilah salah satu penggemar Halaman Fan kami ataupun juga jika anda menggunakan Goolgle+. Klik pada tombol&nbsp;Like&nbsp;pada kolom sisi kanan halaman ini.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Berikan Sumbangan!</strong></p>\r\n<p>Setiap sumbangsih finansial anda, sebesar apapun akan sangat berarti bagi pembayaran hosting situs dan pengelolaannya.</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>Donation bisa ditransfer ke rekening kami di:</p>\r\n<p><strong>BRI UNIT SIDOMULYO TELUK BETUNG&nbsp;</strong></p>\r\n<p><strong>No. Rek. 3562-01-016475-53-9&nbsp;</strong></p>\r\n<p><strong>a.n. Eko Hendratno</strong></p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>Kami memohon kepada Allah atas petunjuk dan pertolongan-Nya serta limpahan rizqi yang terbaik.</p>', 0, 'support-us', 'page', 'standar', 1, 'open', '', '', 1, '', '', 1, 0),
(4, 1, '2013-03-24 12:33:58', 'Cerita dibalik pengembangan CMS ID v 2.2', '<p><span style="line-height: 1.5em;">CMS ID versi 2.2 adalah platform cms, pengembangan dari\r\ncmsid versi sebelumnya.</span><br>\r\n\r\n</p>\r\n\r\n<p>Cmsid pada versi ini sebagian merupakan platform cms code\r\nturunan dari codex wordpress dan digabungkan dengan platform cmsid itu sendiri\r\ndengan cita rasa kedua belah pihak kami selaku pengembang menggunakan hal hal\r\nyang ada platform cms tersebut untuk diterapkan pada cmsid versi ini.</p>\r\n\r\n<p>Cmsid versi ini sendiri merupakan versi yang beda dari versi\r\nversi sebelumnya dikarenakan kami melakukkan penelitian yang cukup panjang\r\nuntuk meneliti bagaimana sebuah platform cms popular saat ini yaitu wordpress\r\nitu bekerja, lalu kami menuangkannya pada cmsid versi baru ini,.. tentunya\r\ndengan beberapa sintax code yang kami bawa pada versi ini. Tapi tidak selamanya\r\nkami menggunakan syntax code itu suatu saat saat pengembangan cmsid lebih jauh\r\nmungkin cmsid akan mengadaptasikan synatax codenya sendiri cepat atau lambat.</p>\r\n\r\n<p>Kenapa kami memilih platform cms wordpress sebagain patner\r\ncodex, ini bukan lain ialah kemudahan pembuatan content yang lebih cepat dan\r\nmudah terutama dalam penggunaannya dengan begitu akan makin cepat dan mudah\r\ncontent akan tersaji untuk digunakan.</p>\r\n\r\n<p><b><br>\r\n\r\n</b></p>\r\n\r\n<p><b>Siapa saja dibalik pengembangan cmsid dan siapa siapa saja\r\nyang telah mendukung,..</b></p>\r\n<p><b><br>\r\n</b></p>\r\n\r\n<p>Saya akan menceritakan kembali sejarah cmsid,. CMS ID di\r\nkembangkan dan didirikan pada tahun 2010 tepatnya pada bulan april dahulu kala\r\ncmsid mesih belum menggunakan domain resmi cmsid.org dahulu domain name cmsid\r\nmasih menggunakan domain gratis yg banyak bertebaran diinternet sampai suatu saat ada supporter yg mendukung\r\ncmsid sampai saat ini, anda bisa cari dan lihat hostname dari cmsid itu sendiri\r\ndan tidak lain dan bukan ialah dutaspace.com lalu siapa dibalik dutaspace.com\r\nitu ialah Sdr.Hadi Mahmud ia adalah reseller hosting yg mendukung penuh cmsid\r\nsampai saat ini, lalu siapa juga yg dibalik pengembangan cmsid itu bukan lain\r\ndan bukan ialah Sdr. Eko seorang Mahasiswa Fakultas Teknik Informatik IBI\r\nDarmajaya Bandar Lampung serta teman teman yang telah memberikan kritik dan\r\nsarannya.</p>\r\n\r\n<p><b><br>\r\n\r\n</b></p>\r\n\r\n<p><b>Kemana arah cmsid itu,..?</b></p>\r\n<p><b><br>\r\n</b></p>\r\n\r\n<p>Yang pastinya cmsid ingin menjadi salah satu platform cms\r\nyang dicintai disisi penggunanya, cmsid\r\nakan selalu terus dan terus dikembangkan dan cmsid juga berencana ingin membuat sebuah produk\r\nbuku ulasan pengembangan cmsid dan waktunya tak dapat saya ditentukan sekarang,\r\nkarena ini merupakan kesiapan disisi penulisan saya, tapi rencana ini sudah\r\nsaya pikir matang matang untuk melaksanakannya, itupun kalau ada yg berkanan\r\nmencicipi buku ini,.. </p>\r\n<p><br>\r\n</p>\r\n\r\n<p>Baiklah itu adalah sepenggal cerita dari saya dibalik\r\npengembangan cmsid ini, jika ada saran dan kritik silahkan kirim ke form form\r\nyang kami sediakan bisa juga melalui forum\r\natau group group kami di fb: <a href="https://www.facebook.com/groups/cmsid/">https://www.facebook.com/groups/cmsid/</a> </p>\r\n\r\n<p>Jika anda salah satu yang berniat bergabung sebagai\r\npengembang atau supporter kami sailahkan hubungi saya di email:id.hpaherba@yahoo.co.id</p>\r\n\r\n<p>Itu sekian prakata dari saya kurang dan lebihnya saya mohon\r\nmaaf,.. salam id by eko</p>\r\n', 12, 'cerita-dibalik-pengembangan-cms-id-v-2-2', 'post', 'standar', 1, 'open', '20130602123301@1.jpg', 'ilst(keterangan gambar)', 1, '', '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `cmsid_tags`
--

CREATE TABLE IF NOT EXISTS `cmsid_tags` (
  `tag_ID` int(11) NOT NULL AUTO_INCREMENT,
  `tag_content` text NOT NULL,
  `tag_post_ID` int(11) NOT NULL,
  PRIMARY KEY (`tag_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cmsid_tags`
--

INSERT INTO `cmsid_tags` (`tag_ID`, `tag_content`, `tag_post_ID`) VALUES
(1, 'cerita, cmsid', 4);

-- --------------------------------------------------------

--
-- Table structure for table `cmsid_users`
--

CREATE TABLE IF NOT EXISTS `cmsid_users` (
  `user_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL,
  `user_author` varchar(80) NOT NULL,
  `user_pass` varchar(64) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_sex` enum('p','l') NOT NULL,
  `user_registered` datetime NOT NULL,
  `user_last_update` datetime NOT NULL,
  `user_activation_key` varchar(60) NOT NULL,
  `user_level` varchar(25) NOT NULL DEFAULT 'user',
  `user_url` varchar(100) NOT NULL,
  `display_name` smallint(250) NOT NULL,
  `user_country` varchar(64) NOT NULL,
  `user_province` varchar(80) NOT NULL,
  `user_avatar` longtext NOT NULL,
  `user_status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cmsid_users`
--

INSERT INTO `cmsid_users` (`user_ID`, `user_login`, `user_author`, `user_pass`, `user_email`, `user_sex`, `user_registered`, `user_last_update`, `user_activation_key`, `user_level`, `user_url`, `display_name`, `user_country`, `user_province`, `user_avatar`, `user_status`) VALUES
(1, 'admin', 'Eko Azza', '21232f297a57a5a743894a0e4a801fc3', 'eko.hendratno@gmail.com', 'l', '2015-06-10 17:11:30', '2018-03-11 13:07:26', '', 'admin', '', 0, 'ID', '', '20150611122427@Koala.jpg', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
