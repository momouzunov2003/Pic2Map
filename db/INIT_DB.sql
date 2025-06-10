CREATE TABLE galleries (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    gallery_id INT NOT NULL,
    url VARCHAR NOT NULL,
    thumbnail_url VARCHAR NOT NULL,
    latitude DOUBLE,
    longitude DOUBLE,
    device_maker VARCHAR(100),
    device_model VARCHAR(100),
    taken_at DATETIME,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gallery_id) REFERENCES galleries(id)
);