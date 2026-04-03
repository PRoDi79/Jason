-- Veritabanı oluştur
CREATE DATABASE IF NOT EXISTS ofishal_db;
USE ofishal_db;

-- 1. Müşteriler
CREATE TABLE musteriler (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kod VARCHAR(20) UNIQUE NOT NULL,
    ad VARCHAR(100) NOT NULL,
    unvan VARCHAR(200),
    vergi_no VARCHAR(20),
    vergi_daire VARCHAR(100),
    adres TEXT,
    il VARCHAR(50),
    ilce VARCHAR(50),
    gsm VARCHAR(20),
    tel VARCHAR(20),
    efatura_tipi ENUM('e-fatura', 'e-arsiv') DEFAULT 'e-arsiv',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Birimler (Excel'deki BIRIM_REF listesi)
CREATE TABLE birimler (
    ref_id INT PRIMARY KEY,
    birim_adi VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Ürünler
CREATE TABLE urunler (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kod VARCHAR(20) UNIQUE NOT NULL,
    ad VARCHAR(100) NOT NULL,
    birim_ref INT NOT NULL,
    varsayilan_kdv INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (birim_ref) REFERENCES birimler(ref_id)
);

-- 4. Faturalar
CREATE TABLE faturalar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fatura_no VARCHAR(30) UNIQUE NOT NULL,
    musteri_id INT NOT NULL,
    tarih DATE NOT NULL,
    vade INT DEFAULT 0,
    odeme_tipi ENUM('Peşin', 'Veresiye') DEFAULT 'Peşin',
    toplam_tutar DECIMAL(15,2) DEFAULT 0,
    toplam_kdv DECIMAL(15,2) DEFAULT 0,
    genel_toplam DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (musteri_id) REFERENCES musteriler(id)
);

-- 5. Fatura Satırları
CREATE TABLE fatura_satirlari (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fatura_id INT NOT NULL,
    urun_id INT NOT NULL,
    urun_adi VARCHAR(100) NOT NULL,
    miktar DECIMAL(15,4) NOT NULL,
    birim_fiyat DECIMAL(15,2) NOT NULL,
    iskonto_orani DECIMAL(5,2) DEFAULT 0,
    kdv_orani INT DEFAULT 1,
    tutar DECIMAL(15,2) NOT NULL,
    sira INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fatura_id) REFERENCES faturalar(id) ON DELETE CASCADE,
    FOREIGN KEY (urun_id) REFERENCES urunler(id)
);

-- 6. Ayarlar
CREATE TABLE ayarlar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firma_adi VARCHAR(200),
    vergi_no VARCHAR(20),
    vergi_daire VARCHAR(100),
    adres TEXT,
    tel VARCHAR(20),
    web_sitesi VARCHAR(100),
    email VARCHAR(100),
    iban VARCHAR(50),
    banka_adi VARCHAR(100),
    logo_path VARCHAR(200),
    varsayilan_kdv INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Loglar
CREATE TABLE loglar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    islem VARCHAR(50),
    detay TEXT,
    kullanici VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Başlangıç birimleri (en çok kullanılanlar)
INSERT INTO birimler (ref_id, birim_adi) VALUES
(22, 'Kilogram'),
(67, 'Adet'),
(52, 'Paket'),
(41, 'Ton'),
(24, 'Litre'),
(21, 'Gram');

-- Başlangıç ayarları
INSERT INTO ayarlar (id, firma_adi, varsayilan_kdv) VALUES (1, 'OFİS HAL', 1);

-- Örnek müşteri
INSERT INTO musteriler (kod, ad, unvan, vergi_no, vergi_daire, adres, il, ilce, gsm, efatura_tipi) VALUES
('KUZEK', 'KUZEY DENIZ SAHA KOMUTANLIGI', 'KUZEY DENIZ SAHA KOMUTANLIGI SARIYER SUBAY GAZINOSU', '6010461326', 'SARIYER VERGİ DAİRESİ', 'MESERBURNU CAD. NO:2 No:1', 'İSTANBUL', 'SARIYER', '0212 123 4567', 'e-arsiv');

-- Örnek ürünler
INSERT INTO urunler (kod, ad, birim_ref, varsayilan_kdv) VALUES
('YUM', 'Yumurta', 67, 1),
('DOM', 'Domates', 22, 1),
('SAL', 'Salatalık', 22, 1),
('PAT', 'Patates', 22, 1),
('BIBER', 'Biber Köy', 22, 1),
('LIMON', 'Limon', 22, 1),
('MAYD', 'Maydanoz', 67, 1),
('ROKA', 'Roka', 67, 1);