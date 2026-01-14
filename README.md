## LiveWebinar Meeting for Moodle

LiveWebinar Meeting is a Moodle activity plugin that integrates Moodle with the LiveWebinar platform  
https://www.livewebinar.com/

### Installation

1. Download this repository as a ZIP archive.
2. In Moodle go to **Site administration → Plugins → Install plugins**.
3. Upload the ZIP file and complete the installation process.
4. Finish the upgrade in **Site administration → Notifications** if prompted.

### Configuration

After installation open  
**Site administration → Plugins → Activity modules → LiveWebinar Meeting**

Provide:
- **Identifier**: `livewebinar`
- **API credentials** (Client ID and Client Secret) from  
  https://app.livewebinar.com/api-apps

Save the settings to activate the integration.

### Usage

1. Open a Moodle course and turn editing on.
2. Select **Add an activity or resource**.
3. Choose **LiveWebinar Meeting** from the list.
4. Configure the meeting to create a LiveWebinar event linked to the course.

### License

GPL v3 or later. See the `LICENSE` file for details.
