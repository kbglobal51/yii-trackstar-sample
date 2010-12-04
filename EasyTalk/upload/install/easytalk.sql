DROP TABLE IF EXISTS `et_ads`;
CREATE TABLE IF NOT EXISTS `et_ads` (
  `ad_id` int(10) NOT NULL AUTO_INCREMENT,
  `position` tinyint(1) NOT NULL,
  `adbody` text NOT NULL,
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_announ`;
CREATE TABLE IF NOT EXISTS `et_announ` (
  `announ_id` int(10) NOT NULL AUTO_INCREMENT,
  `announ_body` varchar(200) NOT NULL,
  `announ_time` int(10) NOT NULL,
  PRIMARY KEY (`announ_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_content`;
CREATE TABLE IF NOT EXISTS `et_content` (
  `content_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `user_nickname` varchar(20) NOT NULL,
  `user_head` varchar(100) NOT NULL,
  `content_body` varchar(500) NOT NULL,
  `posttime` int(10) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT '网页',
  `privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐私',
  `conttype` varchar(10) NOT NULL DEFAULT 'talk' COMMENT '发送的类型/photo/video',
  `status_id` int(10) NOT NULL DEFAULT '0',
  `status_uname` varchar(20) NOT NULL DEFAULT '0',
  `status_unickname` varchar(20) NOT NULL DEFAULT '0',
  `replytimes` smallint(6) NOT NULL DEFAULT '0',
  `zftimes` smallint(6) NOT NULL DEFAULT '0' COMMENT '转发次数',
  `replyshow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否作为一条消息',
  PRIMARY KEY (`content_id`),
  KEY `user_id` (`user_id`),
  KEY `replyshow` (`replyshow`),
  KEY `privacy` (`privacy`),
  FULLTEXT KEY `content_body` (`content_body`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_favorite`;
CREATE TABLE IF NOT EXISTS `et_favorite` (
  `fav_id` int(10) NOT NULL AUTO_INCREMENT,
  `content_id` int(10) NOT NULL,
  `sc_uid` int(10) NOT NULL,
  PRIMARY KEY (`fav_id`),
  KEY `sc_uid` (`sc_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_feedback`;
CREATE TABLE IF NOT EXISTS `et_feedback` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `uname` varchar(20) NOT NULL,
  `unickname` varchar(20) NOT NULL,
  `uhead` varchar(200) NOT NULL,
  `fbtype` tinyint(1) NOT NULL,
  `fbbody` varchar(300) NOT NULL,
  `contect` varchar(50) NOT NULL,
  `dataline` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_friend`;
CREATE TABLE IF NOT EXISTS `et_friend` (
  `fri_id` int(10) NOT NULL AUTO_INCREMENT,
  `fid_jieshou` int(10) NOT NULL,
  `fid_fasong` int(10) NOT NULL,
  PRIMARY KEY (`fri_id`),
  KEY `fid_jieshou` (`fid_jieshou`),
  KEY `fid_fasong` (`fid_fasong`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_invitecode`;
CREATE TABLE IF NOT EXISTS `et_invitecode` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invitecode` varchar(15) NOT NULL COMMENT '邀请码',
  `timeline` int(10) NOT NULL COMMENT '有效期',
  `isused` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被使用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_messages`;
CREATE TABLE IF NOT EXISTS `et_messages` (
  `message_id` int(10) NOT NULL AUTO_INCREMENT,
  `senduid` int(10) NOT NULL COMMENT '发送者uid',
  `sendname` varchar(20) NOT NULL,
  `sendnickname` varchar(20) NOT NULL,
  `sendhead` varchar(100) NOT NULL,
  `sendtouid` int(10) NOT NULL COMMENT '发送给的uid',
  `sendtoname` varchar(20) NOT NULL,
  `sendtonickname` varchar(20) NOT NULL,
  `messagebody` varchar(300) NOT NULL,
  `sendtime` int(10) NOT NULL,
  `isread` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `senduid` (`senduid`),
  KEY `sendtouid` (`sendtouid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_replyto`;
CREATE TABLE IF NOT EXISTS `et_replyto` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '回复记录',
  `user_id` int(10) NOT NULL,
  `content_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `content_id` (`content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_report`;
CREATE TABLE IF NOT EXISTS `et_report` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `reporttype` tinyint(1) NOT NULL,
  `reportbody` varchar(300) NOT NULL,
  `dateline` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_search`;
CREATE TABLE IF NOT EXISTS `et_search` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `searchcont` varchar(20) NOT NULL COMMENT '搜索内容',
  `searchtimes` smallint(6) NOT NULL COMMENT '搜索次数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_settings`;
CREATE TABLE IF NOT EXISTS `et_settings` (
  `web_name` varchar(64) NOT NULL,
  `web_name2` varchar(64) NOT NULL,
  `web_miibeian` varchar(64) NOT NULL,
  `seokey` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `replace_word` text NOT NULL,
  `mail_server` varchar(30) NOT NULL DEFAULT 'smtp.163.com',
  `mail_port` smallint(6) NOT NULL DEFAULT '25',
  `mail_name` varchar(30) NOT NULL DEFAULT 'user@163.com',
  `mail_user` varchar(30) NOT NULL DEFAULT 'user',
  `mail_pass` varchar(30) NOT NULL DEFAULT 'pass',
  `webclose` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关闭网站',
  `closereg` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关闭注册',
  `bindmobile` tinyint(1) NOT NULL DEFAULT '0',
  `openqq` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `et_topic`;
CREATE TABLE IF NOT EXISTS `et_topic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `topicname` varchar(20) NOT NULL COMMENT '话题内容',
  `topictimes` smallint(6) NOT NULL COMMENT '话题次数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_users`;
CREATE TABLE IF NOT EXISTS `et_users` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `user_head` varchar(100) NOT NULL DEFAULT '0',
  `mailadres` varchar(100) NOT NULL,
  `home_city` varchar(16) DEFAULT NULL,
  `live_city` varchar(16) DEFAULT NULL,
  `birthday` varchar(10) DEFAULT NULL,
  `signupdate` varchar(20) NOT NULL,
  `user_gender` varchar(8) DEFAULT NULL,
  `user_info` varchar(255) NOT NULL DEFAULT '这家伙很懒，什么也没写。',
  `isadmin` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` int(10) NOT NULL DEFAULT '0',
  `musicaddr` varchar(200) DEFAULT NULL,
  `msg_num` smallint(6) NOT NULL DEFAULT '0',
  `follow_num` smallint(6) NOT NULL DEFAULT '0',
  `followme_num` smallint(6) NOT NULL DEFAULT '0',
  `fav_num` smallint(6) NOT NULL DEFAULT '0',
  `priread` smallint(6) NOT NULL DEFAULT '0' COMMENT '未读的私信',
  `replyread` smallint(6) NOT NULL DEFAULT '0' COMMENT '未读的回复',
  `gtalk` varchar(30) NOT NULL DEFAULT '0',
  `theme_bgcolor` varchar(7) DEFAULT NULL,
  `theme_pictype` varchar(10) DEFAULT NULL,
  `theme_text` varchar(7) DEFAULT NULL,
  `theme_link` varchar(7) DEFAULT NULL,
  `theme_sidebar` varchar(7) DEFAULT NULL,
  `theme_sidebox` varchar(7) DEFAULT NULL,
  `theme_bgurl` varchar(200) DEFAULT NULL,
  `auth_email` varchar(50) NOT NULL DEFAULT '0',
  `userlock` tinyint(1) NOT NULL DEFAULT '0',
  `lastcontent` varchar(250) NOT NULL DEFAULT '0' COMMENT '最后发表',
  `lastconttime` int(10) NOT NULL DEFAULT '0' COMMENT '最后发表时间',
  `indextop` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否上榜',
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `et_usertemplates`;
CREATE TABLE IF NOT EXISTS `et_usertemplates` (
  `ut_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `theme_bgcolor` varchar(7) NOT NULL,
  `theme_pictype` varchar(10) NOT NULL,
  `theme_text` varchar(7) NOT NULL,
  `theme_link` varchar(7) NOT NULL,
  `theme_sidebar` varchar(7) NOT NULL,
  `theme_sidebox` varchar(7) NOT NULL,
  `theme_upload` tinyint(1) NOT NULL,
  `isopen` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ut_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `et_settings` (`web_name`, `web_name2`, `web_miibeian`, `seokey`, `description`, `replace_word`, `mail_server`, `mail_port`, `mail_name`, `mail_user`, `mail_pass`, `webclose`, `closereg`) VALUES
('EasyTalk微博客', '分享简单生活', '正在备案', 'EasyTalk,微博客,微博,迷你博客,微博系统', 'EasyTalk微博客是国内很有影响的微博系统，用户可通过网页、wap、机器人等发布自己的消息，随时随地关注自己的好友。您的一个状态、一个唠叨、一个回复都将会获得大家的关注！', '政府|达赖|罢工|示威|游行|上访|六四|学潮|共产党|国民党|法轮功|李洪志|美国之音|法轮|出售假币|六合|刻章|法轮|法一轮|大纪元|真善忍|法轮功|阴唇|阴户|强奸|阴茎|阴蒂|乱伦|手淫|做鸡|罗干|三唑仑|麻醉药|麻醉乙醚|短信群发器|帝国之梦|毛一鲜|黎阳平|鬼村|PK黑社会|枪决女犯|出售假币|监听王|昏药|侦探设备|麻醉钢枪|升达毕业证|手机复制|张小平|佳静安定片|蒙汗药粉|古方迷香|强效失意药|子女任职名单|天鹅之旅|盘古乐队|高校群体事件|大学骚乱|高校骚乱|催情药|拍肩神药|春药|枪决现场|出售手枪|麻醉枪|办理文凭|疆独|藏独|高干子弟|高干子女|枪支弹药|血腥图片|反政府|禁书|无界浏览器|特码|成人片|成人电影|三级片|黄海暗杀|政治风波|陈良宇|反民主|政府软弱|政府无能|共狗|回民暴动|手机魔卡|暴力拆迁|轮暴致死|平安夜自杀|轮奸虐杀|暴力镇压|陈水扁|转法轮|达赖|天安门事件|自由亚州|性免费电影|成人贴图|激情电影|色情小说|黄网导航|暴动|九评共产党|性虐待|赵紫阳|点对点裸聊|裸聊合法|松花江污染|汕尾事件|胡新宇|公务员工资|反中|支持台|领导人', 'smtp.163.com', 25, 'admin@163.com', '', '', 0, 0);

INSERT INTO `et_usertemplates` (`ut_id`, `theme_bgcolor`, `theme_pictype`, `theme_text`, `theme_link`, `theme_sidebar`, `theme_sidebox`, `theme_upload`, `isopen`) VALUES
(1, '#acdae5', 'left', '#000000', '#0066cc', '#e2f2da', '#b2d1a3', 1, 1),
(2, '#ccecfa', 'left', '#29453c', '#737311', '#faffe0', '#d5d9bf', 1, 0),
(3, '#3a9dcf', 'left', '#21282B', '#00ABFF', '#346C89', '#346C89', 1, 1),
(4, '#942970', 'repeat', '#660044', '#d957ad', '#ffbfea', '#d9a3c7', 1, 1),
(5, '#CCECFA', 'center', '#FFB300', '#00CDFF', '#303336', '#FF7700', 1, 1),
(6, '#ccecfa', 'center', '#d38603', '#b37132', '#131820', '#d5d9bf', 1, 1);