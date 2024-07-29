Single-Click File Hosting Service
Overview

This web application allows users to quickly and easily upload files. The application features an upload progress bar, a maximum file size limit, an option for Monero cryptocurrency donations, and an admin panel for managing files and users.
Key Features

    Easy File Upload: Users can upload files with just one click.
    Progress Bar: Track the progress of your file upload.
    File Size Limit: Administrators can set a maximum file size for uploads.
    Monero Donations: Option to enable Monero cryptocurrency donations.
    User Management: Admin panel for managing administrators, including creation, deletion, and password changes.
    File Management: Administrators can delete and suspend files as needed.
    Reporting: Users can report files that violate terms of service or infringe on intellectual property rights.
    Reporting Management: Administrators can review reported files and take appropriate action.
    User Accounts: Users can create accounts to manage their uploaded files and access additional features.

Planned Features

    Database Migration: Transition from CSV file storage to a more robust database system for improved scalability and performance.
    Enhanced Storage Options: Implement a feature allowing users to pay for additional storage space. The specifics of this feature are still under consideration and will be detailed in future updates.

Docker Image

To deploy this application using Docker, you can use the official Docker image available at Docker Hub.

You can pull the image with the following command:

bash

docker pull hansat04/filesharing-service

To run the container, use:

bash

docker run -d -p 8080:80 hansat04/filesharing-service

Replace 8080 with your desired port number.
Contributing

Contributions are welcome! Feel free to submit pull requests to enhance functionality or improve the user experience.
License

This project is licensed under the GNU General Public License v3.0 (GPL-3.0).

Terms:

    You can: Use, modify, and distribute the software, provided that all copies include the same license and the source code remains available.
    You must: Share derivative works under the same license and include a copy of the license with any distribution.

For more details, please read the GNU General Public License v3.0.
Contact

For questions or suggestions, please open an issue on GitHub.
Default Login

    admin:admin
