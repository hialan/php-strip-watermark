php-strip-watermark
===================

目的: [政治獻金資料電子化] 去浮水印

License: MIT 

額外的套件: php5-gd

流程:
  1. 縮圖，必要時搭配 SMOOTH filter
  2. 套用高斯模糊
  3. 門檻值過濾
  4. 二元化
  
可調參數:
  1. SMOOTH filter, 後面數字越小越模糊。建議 0~16
  
  2. 高斯模糊
<pre>  
    一般 (加強上下左右)
    |1  2  1|
    |2  4  2|
    |1  2  1|
</pre>

<pre>
    一般 (加強對角)
    |2  1  2|
    |1  4  1|
    |2  1  2|
</pre>

<pre>
    Low: 
    |1  1  1|
    |1  4  1|
    |1  1  1|
</pre>
  
<pre>
    High:
    |2  2  2|
    |2  4  2|
    |2  2  2|
</pre>

  3. 門檻值: 灰階 > 此值，就會設為白色。
  
  4. 二元化，使用 ImageMagic
    - sudo apt-get install imagemagick
    - convert -threshold 70% input.png output.png
