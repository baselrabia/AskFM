# askfm-api
Ask.FM Unofficial API

Website Name : ASK.FM<br />
Description : Ask and answer. Find out what people want to know about you!<br />
Type : API (UnOfficial)<br />

<h2>SETUP</h2>
<p>Upload file ke hosting (local maupun cloud / server), lalu lakukan setting atau panggil class untuk memulai, contoh :</p>
<pre>require("ask.php");
$ask = new askFm();</pre>
<h2>EXAMPLE</h2>
<h3>Login</h3>
Untuk login, demi keamanan menggunakan cookies, anda bisa mendapatkannya dari tamper data ataupun dengan cara apapun.
<pre>$ask->login($cookie);</pre>
<h3>Ask</h3>
<pre>$ask->ask("kouhota", "Ohayou gozaimasu!", 1);</pre>
<p>Untuk parameter ketiga (anon), anda bisa menggunakan 0 atau non anon dengan syarat excute ->login terlebih dahulu. Jika memilih 1, anda bisa melakukannya tanpa login</p>
<h3>Like</h3>
<h4>One</h4>
<pre>$ask->likeOne("kouhota", "139457224382");</pre>
<h4>Mass</h4>
<pre>$ask->likeall("kouhota", 0);</pre>
<p>Untuk parameter kedua (Page), dimulai dari angka 0,1,....</p>
<h3>Logout</h3>
<pre>$ask->logout();</pre>
<h3>Register Account</h3>
<pre>$ask->registerAccount("45.875.874.11:8080", 1, true);</pre>
<p>Parameter pertama merupakan PROXY, masukkan NULL jika tidak digunakan. Parameter kedua anda jumlah akun yang akan dibuat. Parameter ketiga adalah force cookies, function akan mengembalikan COOKIES jika TRUE dan USERPASS jika FALSE.</p>
<h3>Random Question</h3>
<pre>$ask->requestRandom(1);</pre>
<p>Akun anda akan mendapatkan random request sesuai bahasa yang di set, jika bahasa ask.fm anda Jepang, maka anda akan mendapatkan pertanyaan jepang.</p>
<h3>Fetch Question</h3>
<pre>$ask->fetchQuestions();</pre>
<p>Fungsi ini akan menyambil ID ask, kombinasikan dengan ->answer()</p>
<h3>Answer Question</h3>
<pre>$ask->answer($questionID);</pre>
<h2>Notice</h2>
<p>Code ini free/open source dan hanya untuk pembelajaran saja, dimohon untuk tidak menjualnya ataupun yang lainnya, saya tidak bertanggungjawab atas segala kerugian yang ditimbulkan.</p>
<h2>Legal</h2>
<p><b>This code is in no way affiliated with, authorized, maintained, sponsored or endorsed by ASK.FM or any of its affiliates or subsidiaries. Use at your own risk.</b></p>
