``git clone https://github.com/alperen2/abc.git``
``composer install``

#### JWT token için gerekli olan key'lerin oluşturulması
``$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096``
burada bizden ifade isteyecek, ``path`` yazıyoruz.
``$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout``
Burada doğrulamak için tekrar `path` yazıyoruz.
Eğer path değil de başka bir ifade yazarsak bunu .env dosyası içerisinde `JWT_PASSPHRASE` key'ine value olarak vermeliyiz.

#### Veritabanımızın oluşturulması
``$ docker-compose up -d``
Bu komut ile docker'ımızı ayağa kaldırıyoruz. veritabanı servisimizin çalışacağı container'ı başlatacak.

``$ php bin/console make:migration``
``$ php bin/console doctrine:migrations:migrate``
bu komutlarla veritabanımız oluşturulacak.
####
``$ php bin/authouser`` 
Bu komut ile 3 adet kullanıcı oluşturulacak, ufak bir script yazdım. Kullanıcı eklemek ile uğraşmayın diye.
Hepsinin parolası ``1234``.

#### kullanıcılar
* socrates
* epictetos
* zenon

``$ symfony serve`` ile symfony sunucumuzu ayağa kaldırıyoruz.

``abc.postman_collection.json`` dosyamızı postman'a import edip gerekli endpoint'leri görebiliriz.

ilk önce check login ile login olalım.
