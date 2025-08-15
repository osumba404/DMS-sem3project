
```*(The full implementation would be similar to other `delete` endpoints)*

---

### Part III: User Management & Reporting

#### 7. Get Specific User Details

**File Location:** `backend/api/admin/user_get_details.php`
```php
<?php
/**
 * API Endpoint: Admin - Get User Details
 * Method: GET
 * Parameter: ?user_id=
 */
header('Content-Type: application/json');
require_once '../../config/db_connect.php';
function send_response($status, $message, $data=null) { /* ... same as above ... */ }
// Admin auth check...

$user_id = $_GET['user_id'] ?? null;
if (empty($user_id)) send_response(400, 'Bad Request. user_id is required.');

// Get user data
$stmt = $conn->prepare("SELECT id, full_name, email, phone_number, is_safe, is_tourist, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) send_response(404, 'User not found.');
$user_data = $result->fetch_assoc();
$stmt->close();

// Get emergency contacts
$stmt = $conn->prepare("SELECT name, phone_number, relationship FROM emergency_contacts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$contacts_result = $stmt->get_result();
$contacts = [];
while ($row = $contacts_result->fetch_assoc()) {
    $contacts[] = $row;
}
$user_data['emergency_contacts'] = $contacts;
$stmt->close();

send_response(200, 'User details fetched.', $user_data);
$conn->close();
?>