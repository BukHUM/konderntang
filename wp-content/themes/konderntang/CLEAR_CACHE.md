# วิธี Clear Cache สำหรับ Admin UI

## ปัญหา: การแสดงผลยังเป็นแบบเดิม

ถ้าการแสดงผลของ Theme Settings ยังเป็นแบบเดิม (มี blue header bar) แสดงว่ายังมี cache อยู่

## วิธีแก้ไข:

### 1. Hard Refresh Browser
- **Windows/Linux**: กด `Ctrl + Shift + R` หรือ `Ctrl + F5`
- **Mac**: กด `Cmd + Shift + R`

### 2. Clear Browser Cache
- **Chrome**: 
  - กด `Ctrl + Shift + Delete`
  - เลือก "Cached images and files"
  - กด "Clear data"
  
- **Firefox**:
  - กด `Ctrl + Shift + Delete`
  - เลือก "Cache"
  - กด "Clear Now"

### 3. Clear WordPress Cache (ถ้ามี Plugin Cache)
- **WP Super Cache**: Settings → WP Super Cache → Delete Cache
- **W3 Total Cache**: Performance → Dashboard → Empty All Caches
- **WP Rocket**: WP Rocket → Clear Cache
- **LiteSpeed Cache**: LiteSpeed Cache → Toolbox → Purge All

### 4. ตรวจสอบว่าไฟล์ถูก Load
1. เปิด Developer Tools (`F12`)
2. ไปที่แท็บ **Network**
3. Refresh หน้า admin (`F5`)
4. ค้นหา `admin.css` และ `admin.js`
5. ตรวจสอบว่า Status เป็น **200** และถูก load

### 5. ตรวจสอบ Version Number
- ดูที่ URL ของ CSS/JS ใน Network tab
- ควรมี query string เช่น `?ver=1234567890.1234567890`
- ถ้าไม่มีหรือเป็น version เดิม แสดงว่า cache ยังอยู่

### 6. ใช้ Incognito/Private Mode
- เปิด Incognito/Private window
- Login เข้า WordPress admin
- ตรวจสอบว่าแสดงผลถูกต้องหรือไม่

### 7. Clear PHP OpCache (ถ้าใช้)
- Restart web server (Apache/Nginx)
- หรือ clear opcache ผ่าน PHP

## สิ่งที่ถูกอัพเดต:

✅ Header เปลี่ยนจาก gradient box เป็น `konderntang-dashboard-title`
✅ Tab navigation มี icons
✅ Form sections อยู่ใน widget boxes
✅ CSS มี cache busting (ใช้ timestamp)

## หมายเหตุ:

ไฟล์ CSS/JS ใช้ `filemtime()` + `time()` สำหรับ cache busting ซึ่งจะ force reload ทุกครั้งที่ refresh หน้า
