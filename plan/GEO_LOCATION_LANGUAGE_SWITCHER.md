# Geo-Location Detection & Language Switcher Modal - Plan

## วัตถุประสงค์
เพิ่มฟีเจอร์สำหรับ:
1. **Geo-Location Detection**: ตรวจสอบประเทศของผู้ใช้และแนะนำภาษาที่เหมาะสม
2. **Language Switcher Modal**: แสดง modal popup สำหรับเลือกภาษา (รองรับหลายภาษา)

## ขอบเขตการทำงาน

### 1. Geo-Location Detection

#### 1.1 การตรวจสอบประเทศผู้ใช้
- ใช้ GeoIP API เพื่อตรวจสอบ IP address
- แนะนำภาษาตามประเทศที่ตรวจพบ
- เก็บ preference ใน cookie/localStorage
- รองรับการปิดการใช้งาน

#### 1.2 API Options
**แนะนำ:**
- **ipapi.co** (Free tier: 1,000 requests/day)
- **ip-api.com** (Free tier: 45 requests/minute)
- **MaxMind GeoIP2** (Commercial, มีความแม่นยำสูง)

**Fallback:**
- ถ้า API ล้มเหลว → ใช้ default language
- ถ้าไม่สามารถตรวจสอบได้ → แสดง language selection modal

#### 1.3 Country to Language Mapping
```
Thailand (TH) → Thai (th)
United States (US) → English (en)
United Kingdom (GB) → English (en)
France (FR) → French (fr)
Japan (JP) → Japanese (ja)
Laos (LA) → Lao (lo)
Germany (DE) → German (de)
Russia (RU) → Russian (ru)
```

### 2. Language Switcher Modal

#### 2.1 Display Modes
- **Dropdown** (default): เหมาะกับ 2-3 ภาษา
- **Modal Popup**: เหมาะกับ 4+ ภาษา

#### 2.2 Modal Features
- แสดงรายการภาษาทั้งหมดจาก Polylang
- แสดง flag icons
- Search box (สำหรับกรณีที่มีหลายภาษา)
- Smooth animations
- Responsive design
- ปิดเมื่อคลิกนอก modal หรือกด ESC

### 3. Theme Settings Integration

#### 3.1 Settings Location
**Theme Settings → Header Section**

#### 3.2 Settings Fields

```
[ ] Enable Geo-Location Detection
    Default Language: [Dropdown - จาก Polylang languages]
    [ ] Auto-redirect on first visit
    [ ] Show language selection modal if auto-detected

Language Switcher Style:
    ( ) Dropdown (default)
    ( ) Modal Popup
    
[ ] Show Flags
[ ] Show Search Box (Modal only)
Modal Title: [Text input - default: "เลือกภาษา"]
```

### 4. Technical Implementation

#### 4.1 Files to Create

**PHP Files:**
- `inc/geo-location.php` - Geo-location detection logic
- `template-parts/components/language-switcher.php` - Language switcher component

**JavaScript Files:**
- `assets/js/geo-location.js` - Geo-detection และ auto-redirect
- `assets/js/language-switcher.js` - Modal functionality

**CSS Files:**
- `assets/css/language-switcher.css` - Styles สำหรับ modal

#### 4.2 Files to Modify

- `inc/admin-settings.php` - เพิ่ม settings fields
- `inc/enqueue-scripts.php` - Enqueue scripts และ styles
- `template-parts/components/navigation.php` - แทนที่ language selector เดิม

### 5. Polylang Integration

#### 5.1 Required Functions
```php
// ตรวจสอบว่า Polylang active หรือไม่
if ( function_exists( 'pll_the_languages' ) ) {
    // ดึงรายการภาษา
    $languages = pll_the_languages( array( 'raw' => 1 ) );
    
    // ภาษาปัจจุบัน
    $current_lang = pll_current_language();
    
    // เปลี่ยนภาษา
    pll_set_language( $lang_code );
}
```

#### 5.2 Language Data Structure
```php
array(
    'slug' => 'th',
    'name' => 'ไทย',
    'flag' => '🇹🇭',
    'url' => 'https://example.com/th/',
    'current_lang' => true,
    'no_translation' => false
)
```

### 6. User Experience Flow

#### 6.1 First Visit (Geo-Detection Enabled)
1. ผู้ใช้เข้าครั้งแรก
2. ตรวจสอบ geo-location จาก IP
3. แนะนำภาษาตามประเทศ
4. แสดง modal (ถ้าเปิดใช้งาน)
5. ผู้ใช้เลือกภาษา
6. บันทึก preference ใน cookie
7. Redirect ไปยังภาษาที่เลือก

#### 6.2 Returning Visitor
1. ตรวจสอบ cookie/localStorage
2. ใช้ภาษาที่เลือกไว้ก่อนหน้า
3. ไม่แสดง modal (ยกเว้นผู้ใช้เปลี่ยนเอง)

#### 6.3 Manual Language Change
1. ผู้ใช้คลิก language switcher
2. แสดง dropdown หรือ modal
3. เลือกภาษาใหม่
4. อัพเดท preference
5. Redirect ไปยังภาษาที่เลือก

### 7. Privacy & GDPR Compliance

#### 7.1 Privacy Considerations
- แจ้งผู้ใช้เกี่ยวกับ geo-detection
- รองรับการปิดการใช้งาน
- ไม่เก็บข้อมูลส่วนบุคคล (ใช้แค่ IP address)
- Cookie consent (ถ้าใช้ cookie)

#### 7.2 Implementation
- แสดง notification เมื่อเปิด geo-detection ครั้งแรก
- มี link ไปยัง privacy policy
- รองรับ "Do Not Track" header

### 8. Performance Optimization

#### 8.1 Caching Strategy
- Cache geo-location result ใน cookie (7 วัน)
- Cache API response ใน transient (1 ชั่วโมง)
- Lazy load geo-detection script

#### 8.2 API Usage
- ใช้ async/await สำหรับ API calls
- Timeout: 3 seconds
- Retry: 1 time (ถ้าล้มเหลว)

### 9. Error Handling

#### 9.1 Fallback Scenarios
- API timeout → ใช้ default language
- API error → ใช้ default language
- Invalid country code → ใช้ default language
- Polylang inactive → แสดง error message

#### 9.2 Logging
- Log API errors (ถ้า WP_DEBUG enabled)
- Log geo-detection results (optional, สำหรับ debugging)

### 10. UI/UX Design

#### 10.1 Modal Design
- **Size**: Max-width 500px (responsive)
- **Position**: Center screen
- **Backdrop**: Dark overlay (opacity 0.5)
- **Animation**: Fade in/out
- **Close Button**: X button ที่มุมขวาบน

#### 10.2 Language List
- **Layout**: Grid (2 columns on mobile, 3-4 on desktop)
- **Item Style**: Card with flag + language name
- **Hover Effect**: Scale up + shadow
- **Current Language**: Highlighted with border

#### 10.3 Search Box (Modal only)
- **Placeholder**: "ค้นหาภาษา..."
- **Position**: Top of modal
- **Functionality**: Filter languages in real-time

### 11. Testing Checklist

#### 11.1 Geo-Location Detection
- [ ] ตรวจสอบจาก IP ต่างประเทศ (US, JP, FR)
- [ ] ตรวจสอบ fallback เมื่อ API ล้มเหลว
- [ ] ตรวจสอบ cookie storage
- [ ] ตรวจสอบ auto-redirect
- [ ] ตรวจสอบ modal display

#### 11.2 Language Switcher
- [ ] Dropdown mode ทำงานถูกต้อง
- [ ] Modal mode ทำงานถูกต้อง
- [ ] Search box filter ทำงาน
- [ ] Responsive design
- [ ] Animation smooth
- [ ] Close modal เมื่อคลิกนอก

#### 11.3 Polylang Integration
- [ ] ดึงรายการภาษาถูกต้อง
- [ ] เปลี่ยนภาษาได้
- [ ] URL เปลี่ยนตามภาษา
- [ ] Content แสดงตามภาษา

#### 11.4 Settings
- [ ] Settings save ได้
- [ ] Settings load ได้
- [ ] Enable/disable geo-detection
- [ ] Switch between dropdown/modal

### 12. Implementation Steps

#### Phase 1: Settings & Basic Structure
1. เพิ่ม settings fields ใน `admin-settings.php`
2. สร้าง `geo-location.php` (basic structure)
3. สร้าง `language-switcher.php` component
4. Update `navigation.php` เพื่อใช้ component ใหม่

#### Phase 2: Geo-Location Detection
1. Implement GeoIP API call
2. Country to language mapping
3. Cookie/localStorage storage
4. Auto-redirect logic

#### Phase 3: Language Switcher Modal
1. Create modal HTML structure
2. Implement JavaScript for modal
3. Add CSS styles
4. Integrate with Polylang

#### Phase 4: Testing & Refinement
1. Test all scenarios
2. Fix bugs
3. Optimize performance
4. Add error handling

### 13. Dependencies

#### 13.1 Required Plugins
- **Polylang** (active)

#### 13.2 External APIs
- GeoIP API (ipapi.co หรือ ip-api.com)

#### 13.3 WordPress Functions
- `wp_remote_get()` - สำหรับ API calls
- `setcookie()` - สำหรับ cookie storage
- `get_transient()` / `set_transient()` - สำหรับ caching

### 14. Future Enhancements

#### 14.1 Advanced Features
- Browser language detection (fallback)
- Custom country-language mapping
- Multiple API providers (fallback chain)
- Analytics tracking (language selection)
- A/B testing (modal vs dropdown)

#### 14.2 Performance
- Service Worker สำหรับ offline detection
- Prefetch language pages
- CDN integration

### 15. Notes

- Geo-detection เป็น optional feature (ผู้ใช้สามารถปิดได้)
- Modal popup เหมาะกับเว็บที่มีหลายภาษา (4+)
- Dropdown เหมาะกับเว็บที่มี 2-3 ภาษา
- ต้องทดสอบกับ Polylang versions ต่างๆ
- ต้องรองรับ caching plugins (WP Super Cache, W3 Total Cache)

---

## สรุป

ฟีเจอร์นี้จะช่วยให้เว็บไซต์:
- **Professional**: มี geo-detection เหมือนเว็บ global
- **User-friendly**: แนะนำภาษาที่เหมาะสม
- **Flexible**: รองรับทั้ง dropdown และ modal
- **Privacy-compliant**: รองรับ GDPR และ privacy concerns
