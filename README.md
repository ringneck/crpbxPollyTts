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

### 사전설치
```html
  apt-get -y update
  apt-get install sox libsox-fmt-mp3
```

### 다운로드 및 설치
```html
  git clone https://github.com/ringneck/crpbxPollyTts.git
  cp -rp crpbxPollyTts/crpbxPollyTts.php /var/lib/asterisk/agi-bin/
  chmod 755 /var/lib/asterisk/agi-bin/crpbxPollyTts.php
  cp -rp crpbxPollyTts/vendor /var/lib/asterisk/agi-bin/
  chown -R asterisk. /var/lib/asterisk/agi-bin/vendor
  chown -R asterisk. /var/lib/asterisk/agi-bin/vendor/
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
 same => n,Answer()
 same => n,Set(YEAR=${STRFTIME(${EPOCH},,%Y)})
 same => n,Set(MONTH=${STRFTIME(${EPOCH},,%m)})
 same => n,ExecIf($["${MONTH:0:1}" = "0"]?Set(MONTH=${MONTH:-1}):Set(MONTH=${MONTH}))
 same => n,Set(DAY=${STRFTIME(${EPOCH},,%d)})
 same => n,ExecIf($["${DAY:0:1}" = "0"]?Set(DAY=${DAY:-1}):Set(DAY=${DAY}))
 same => n,Set(HOUR=${STRFTIME(${EPOCH},,%H)})
 same => n,Set(MINUTE=${STRFTIME(${EPOCH},,%M)})
 same => n,Set(TIMETTS=안녕하세요? 지금 시간은 ${YEAR}년, ${MONTH}월, ${DAY}일, ${HOUR}시, ${MINUTE}분, 입니다 )
 same => n,Set(TTSINTRO=제 이름은 알렉사가 아니고, 서연이에요. 제 목소리 어 떠세요?  이쁜가요? )
 same => n,Set(TTSWORD=고객에게 얼쑤의 기운을 드립니다. 얼쑤!  지화자!)
 same => n,Set(TTS=${TIMETTS}, ${TTSINTRO}, ${TTSWORD})
 same => n,AGI(crpbxPollyTts.php,"=${TTS})
 same => n,Hangup()
```

### 적용 및 테스트

```html
root@crpbx:~# asterisk -vvvvr
crpbx*CLI> reload
```

### 다이얼 55555
