#!/usr/bin/env python
#-*- coding: utf-8 -*-
import Image, ImageFont, ImageDraw

ascii=dict()

def build_bitmap(c):
    im = Image.new("RGBA", (20, 20), color="#ffffff")
    dr = ImageDraw.Draw(im)
    dr.text((0,0),c,font=font, fill="#000000")
    bits = []
    for i in range(0,19):
        for j in range(0, 19):
            (r,g,b,a) = im.getpixel((i,j))
            bits.append(r+g+b+a)
    return bits

def find_minimum(bitmap):
    min_char = None
    min_sum = None
    for a in ascii.keys():
        sum = 0
        for i in range(0, len(bitmap)):
            sum += abs(bitmap[i] - ascii[a][i])
        if min_sum is None or min_sum > sum:
            min_sum = sum
            min_char = a
    return min_char
font = ImageFont.truetype("/var/lib/defoma/x-ttcidfont-conf.d/dirs/TrueType/DejaVuSans.ttf", 10)

for i in range (ord('A'), ord('z')):
    ascii[i] = build_bitmap(chr(i))

ascii[ord(' ')] = build_bitmap(' ')

text = u"Áá Àà Ăă Ắắ Ằḅ Ḇḇ Ƀƀ ᵬ ᶀ Ɓɓ Ƃƃ Ćć Ĉĉ Čč Ċċ Çç Ḉḉ Ȼȼ Ƈƈ ɕ Ďď Ḋḋ Ḑḑ Ḍḍ Ḓḓ Ḏḏ Đđ Ð Ęę Ēē Ḗḗ Ḕḕ Ẻẻ Ȅȅ Ȇȇ Ẹẹ Ệệ Ḙḙ Ḛḛ Ģģ Ḡḡ Ǥǥ ᶃ Ⱨⱨ Íí Ìì Ĭĭ Îî Ǐǐ Ïï Ḯḯ Ĩĩ İi Įį Īī Ỉỉ Ȉȉ Ȋȋ Ịị Ḭḭ Iᶄ Ƙƙ Ⱪⱪ Ĺĺ Ľľ Ļļ Ḷḷ Ḹḹ Ḽḽ Ḻḻ Łł Ł̣ ł̣ Ŀŀ Ƚƚ Ⱡⱡ Ɫɫ Ꝉꝉ Ꝇꝇ ɬ ᶅ ɭ ȴ Ḿḿ Ṁṁ Ṃṃ ᵯ ᶆ ɱ Ńń Ǹǹ Ňň Ññ Ṅṅ Ņņ Ṇṇ Ṋṋ Ṉṉ ᵰ Ɲ"

for letter in text:
    b = build_bitmap(letter)
    c = chr(find_minimum(b))
    if c!= ' ':
        print letter + " -> " + c
