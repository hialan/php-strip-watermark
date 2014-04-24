php-strip-watermark
===================

目的: [政治獻金資料電子化] 去浮水印

License: MIT http://g0v.mit-license.org/

額外的套件: php5-gd

流程:
  1. 縮圖，必要時搭配 SMOOTH filter
  2. 套用高斯模糊
  3. 門檻值過濾
  4. 二元化
  
可調參數:
  1. SMOOTH filter, 後面數字越小越模糊。建議 0~16
  
  2. 高斯模糊

  3. 門檻值: 灰階 > 此值，就會設為白色。
  
  4. 二元化，使用 ImageMagic
    - sudo apt-get install imagemagick
    - convert -threshold 70% input.png output.png

OCR: (tesseract)

   $ sudo apt-get install tesseract-ocr tesseract-ocr-chi-tra

