<?php

return [
    'migration' => [
        'id' => '자동 증가 고유 번호',
        'receiver' => '수신 정보',
        'purpose' => '인증 코드 발급 목적',
        'user_id' => '회원 고유 번호, 0 은 비회원',
        'code' => '인증 코드',
        'expires_at' => '만료 일시',
        'attempts' => '인증 시도 횟수',
        'is_verified' => '인증 여부 (0: 인증 전, 1: 인증 완료)',
        'verified_at' => '인증 완료 일시',
        'is_revoked' => '취소 여부 (0: 취소 안됨, 1: 취소)',
        'revoked_at' => '취소 일시',
        'idx_receiver_purpose' => '수신자 정보 및 목적',
    ]
];
