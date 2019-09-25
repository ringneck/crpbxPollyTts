# crpbxPollyTts (AMAZON AWS POLLY ON CRPBX)

### http://www.olsoo.com

```html
###############################################
#   고객에게 얼쑤의 기운을 드립니다.  얼쑤! 지화자!      #
###############################################
#        ____  __    ____ ____ ____  ____     #
#       / __ \/ /   / __ / __ / __ \/ __ \    #
#      / / / / /   / /  / /  / / / / / / /    #
#     / / / / /    \_ \ \_ \/ / / / / / /     #
#    / /_/ / /___ __/ /__/ / /_/ / /_/ /      #
#    \____/_____/____/____/\____/\____/       #
#                                             #
#        Contact : +82 10 9955 2471           #
###############################################
#     CR-PBX DIY 5.X    norman@olssoo.com     #
#  2014-2019|Powered by © OLSSOO FACTORY Inc. #
###############################################
```

### 다운로드 및 설치
```html
  git clone https://github.com/ringneck/crpbxPollyTts.git
   cp -rp crpbxPollyTts /var/lib/asterisk/agi-bin/
  chown -R asterisk. /var/lib/asterisk/agi-bin/crpbxPollyTts
```

### 설정 변경 /var/lib/asterisk/agi-bin/crpbxPollyTts/crpbxPollyTts.php
#### Amazon_key, Amazon_secret

```html
 vim /var/lib/asterisk/agi-bin/crpbxPollyTts/crpbxPollyTts.php
 $Amazon_key    = "XXXXXXXXXXXXXXXXXXXX";
 $Amazon_secret = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
```

### 커스텀 다이얼플랜 추가 
```html
 vim /etc/asterisk/extensions_custom.conf

;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;; AMAZON AWS POLLY TTS ;;;;;;;;;;
;;;;; AGI(crpbxPollyTts.php,"${TTS}") ;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
exten => 55555,1,NoOp(Test AWS Polly TTS !!!)
 same => n,Set(YEAR=${STRFTIME(${EPOCH},,%Y)})
 same => n,Set(MONTH=${STRFTIME(${EPOCH},,%m)})
 same => n,Set(DAY=${STRFTIME(${EPOCH},,%d)})
 same => n,Set(HOUR=${STRFTIME(${EPOCH},,%H)})
 same => n,Set(MINUTE=${STRFTIME(${EPOCH},,%M)})
 same => n,Set(TIMETTS=YEAR년 MONTH월 DAY일 HOUR시 MINUTE분입니다. 전화 주셔서 감사합니다)
 same => n,Set(TTSINTRO=안녕하세요? 제 이름은 서연이에요. 제 목소리 이쁜가요 ? 폴리 연동이 잘 되었습니다)
 same => n,Set(TTS=${TIMETTS} ${TTSINTRO})
 same => n,Hangup()
```

### 적용 및 테스트

```html
root@crpbx:~# asterisk -vvvvr
crpbx*CLI> reload
```
