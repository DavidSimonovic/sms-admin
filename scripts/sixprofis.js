const puppeteer = require('puppeteer');
const mysql = require('mysql2/promise');

async function scrapeSixprofi() {
    const baseUrl = 'https://www.6profis.de/includes/webservices/__suche_get.php?suche=Berlin%2C%20Deutschland&action=suche&lat=52.52000659999999&lng=13.404954&pelo=i_alle&detailsuch=1&sonder=&katchips=&latalt=52.52000659999999&lngalt=13.404954&suchealt=Berlin%2C%20Deutschland&lmt_gesamt=892&lmt=';

    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    let connection;

    try {
        let paginationNumber = 0;
        const totalPages = 10; // Example total pages, replace with actual logic

        while (paginationNumber < totalPages * 42) {
            const pageUrl = `${baseUrl}${paginationNumber}&wia=geo&bas=1&suche2=&ausschl=IEFORCBPcnQgIT0gJ0Jlcmxpbicg`;
            await page.goto(pageUrl, { waitUntil: 'domcontentloaded' });

            // Wait for a specific condition or delay to ensure content is loaded
            await new Promise(resolve => setTimeout(resolve, 2000)); // Example: wait for 2 seconds

            // Extract href URLs using page.evaluate() within the context of the page
            const hrefUrls = await page.evaluate(() => {
                const urls = [];
                const links = document.querySelectorAll('a[href]');

                links.forEach(link => {
                    if (!link.href.includes('page=')) {
                        urls.push(link.href);
                    }
                });

                return urls;
            });

            // Connect to MySQL database (replace with your MySQL connection details)
            connection = await mysql.createConnection({
                host: 'localhost',
                user: 'sail',
                password: 'password',
                database: 'laravel'
            });
            console.log('Connected to MySQL');

            // Insert each URL into the MySQL database if it does not already exist
            for (const url of hrefUrls) {
                try {
                    const [rows] = await connection.execute('SELECT COUNT(*) AS count FROM sixprofis_urls WHERE url = ?', [url]);
                    const urlExists = rows[0].count > 0;

                    if (!urlExists) {
                        await connection.execute('INSERT INTO sixprofis_urls (url) VALUES (?)', [url]);
                        console.log(`Inserted URL: ${url}`);
                    } else {
                        console.log(`URL already exists: ${url}`);
                    }
                } catch (error) {
                    console.error(`Error inserting URL ${url}:`, error);
                }
            }

            paginationNumber += 42; // Adjust pagination logic as needed based on your site's pagination

            // Optional: Break the loop if no URLs are found on the current page
            if (hrefUrls.length === 0) {
                console.log('No more links found. Scraping complete.');
                break;
            }
        }
    } catch (error) {
        console.error('Error during scraping:', error);
    } finally {
        await browser.close();
        if (connection) {
            try {
                await connection.end(); // Close MySQL connection if open
                console.log('MySQL connection closed.');

            } catch (error) {
                console.error('Error closing MySQL connection:', error);
            }
        }
    }
}

// Call the scraping function
scrapeSixprofi()
    .then(() => {
        console.log('Scraping complete');
        process.exit();
    })
    .catch(error => {
        console.error('Script execution error:', error);
        process.exit();
    });
