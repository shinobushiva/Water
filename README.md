# 熊本地震で発生した水不足を解消するためのWaterMapの開発用です。

## 使用方法
[URL](http://mizuderu.info/ "http://mizuderu.info/")を開く。  

熊本を中心として水の状況を以下の色で表示しています。  

* 赤 :水が出ない
* 青 :水は出る
* 燈 :水の提供可能

新しく情報を登録したいときは、画面上の「投稿する」をクリックします。そうすると画面が切り替わります。  
ここで選択肢から「水が出ない」「水が出る」「水の提供可能」の３つからどれか選んで地図状にその位置をクリックすることで地点の設定ができます。  
スマホなど現在地を取得できる機器であれば「現在地を設定」で今の位置を設定できます。  
最後に「投稿」ボタンを押せが地図上にその情報が表示されます。

##Twitter

ハッシュタグ #水出る #熊本地震  

## サーバスペック
Beanstalk  
64bit Amazon Linux 2016.03 v2.1.0 running PHP 5.6  

## Docker開発環境
1. docker-engineインストール  
https://docs.docker.com/engine/installation/
2. docker-composeインストール  
https://docs.docker.com/compose/install/
3. 起動  

```sh
docker-compose up -d
```

### 環境情報
下記の通りローカルに立ち上がる
- apache + php5.6が `80` ポート
- MySQLが `3306` ポート
- Memcachedが `11211` ポート

### MySQLのスキーマ準備

```sh
mysql -h 127.0.0.1 -u root -p
Enter password: password

mysql> create database water;
mysql> use water;
mysql> water.sqlのSQLを実行する
```

### .envによる環境変数設定
.env.templateを.envにリネーム後、各環境変数を設定。
現状おおまかに下記の環境変数がある。

- MySQL (DB...)
- AWS S3 (AWS...)
- Digest認証 (DIGEST...)
- Memcached (SESSION...)
- CSRF対策 (CSRF...)

## 開発メンバー
菊川 稀玲,
和泉 信生,
山ノ内 祥訓,
星野 雅治,
新垣 圭祐,
村上 卓,
前川 奈々,
石塚 隆博,
豊田 啓介,
内藤 豊,
松岡 祥仁,
テスター見習い　森下 功啓,
関西PHP-UGの有志,
JAWS-UG(AWS Users Group-Japan)の有志,
AWSJ(AWS Japan)の有志
