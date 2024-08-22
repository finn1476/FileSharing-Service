# Single-Click File Hosting Service

## Overview

This web application allows users to quickly and easily upload files. The application features an upload progress bar, a maximum file size limit, an option for Monero cryptocurrency donations, and an admin panel for managing files and users.

## Key Features

- **Easy File Upload**: Users can upload files with just one click.
- **Progress Bar**: Track the progress of your file upload.
- **File Size Limit**: Administrators can set a maximum file size for uploads.
- **File Download Speed**: Administrators can set a download speed for downloads.
- **Monero Donations**: Option to enable Monero cryptocurrency donations.
- **User Management**: Admin panel for managing administrators, including creation, deletion, and password changes.
- **File Management**: Administrators can delete and suspend files as needed.
- **Reporting**: Users can report files that violate terms of service or infringe on intellectual property rights.
- **Reporting Management**: Administrators can review reported files and take appropriate action.
- **User Accounts**: Users can create accounts to manage their uploaded files and access additional features.
- **Encryption**: Files are Stored encrypted on the Server.
- **Paid Storage Upgrade**: Users can pay for additional storage space if they exceed the default storage limit.

## Planned Features

- **Database Migration**: Transition from CSV file storage to a more robust database system for improved scalability and performance.

## Docker Deployment

### Docker Image

To deploy this application using Docker, use the official Docker image available on [Docker Hub](https://hub.docker.com/repository/docker/hansat04/filesharing-service/general).

### Persistent Data

Map the following volumes to ensure persistent data:

- `/var/www/html/Files`
- `/var/www/html/Speicher`
- `/var/www/html/Uploaded_Files`
- `/var/www/html/admin`
#### Download the Image

Use the following command to download the Docker image:

`docker pull hansat04/filesharing-service:tag`

#### Start the Container

To run the container, use this command:

`docker run -d -p 8080:80 hansat04/filesharing-service:tag`

- `-d`: Runs the container in detached mode.
- `-p 8080:80`: Maps port 80 in the container to port 8080 on your host machine.

You can then access the application in your browser at `http://<ip>:8080`.
## Contributing

Contributions are welcome! Feel free to submit pull requests to enhance functionality or improve the user experience.

## License

This project is licensed under the [GNU General Public License v3.0 (GPL-3.0)](https://www.gnu.org/licenses/gpl-3.0.html).

**Terms:**

- **You can**: Use, modify, and distribute the software, provided that all copies include the same license and the source code remains available.
- **You must**: Share derivative works under the same license and include a copy of the license with any distribution.

For more details, please read the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html).

## Contact

For questions or suggestions, please open an [issue](https://github.com/finn1476/FileSharing-Service/issues) on GitHub.

## Default Login
- admin:admin

## Photo Gallery
- Index
![grafik](https://github.com/user-attachments/assets/c811ecf3-1c1a-4134-8299-374a1858621a)


- Download
![grafik](https://github.com/user-attachments/assets/06b756c1-ba97-4669-9513-45962ca1b0a5)


- Subscription
![grafik](https://github.com/user-attachments/assets/1ca7abdd-49ee-4561-8a44-9df9e575c35f)

- Report
![grafik](https://github.com/user-attachments/assets/4e5c1324-a047-43d9-bf99-8f3938354c7b)



- User
![grafik](https://github.com/user-attachments/assets/521e2b1c-016e-427e-8b13-dae930b26d26)
![grafik](https://github.com/user-attachments/assets/162861fd-2221-4782-a280-3b3a2707c2a9)


- Admin
![grafik](https://github.com/user-attachments/assets/33c2601e-8724-4647-bf13-935c7385d8c8)
![grafik](https://github.com/user-attachments/assets/8bdf3979-361d-4ac2-b6e0-0baf642dc951)
![grafik](https://github.com/user-attachments/assets/8e5fadc4-fd20-43c4-ace3-25277ac3d64e)


