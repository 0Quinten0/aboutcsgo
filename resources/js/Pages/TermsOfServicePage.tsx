import React from "react";
import useHelmet from "../hooks/useHelmet";
import { Typography, Container, useTheme } from "@mui/material";
import { Head } from "@inertiajs/react";

const TermsOfService: React.FC = () => {
    const helmet = useHelmet();

    const theme = useTheme();

    return (
        <>
            <style>
                {`
          body {
            margin: 0;
            padding: 0;
          }
        `}
            </style>

            <Head>
                <title>Terms Of Service | AboutCSGO</title>
                <meta
                    head-key="description"
                    name="description"
                    content={`The terms of service of AboutCSGO`}
                />
                <link
                    head-key="canonical"
                    rel="canonical"
                    href={`https://www.aboutcsgo.com/terms-of-service`}
                />
            </Head>

            <Container
                style={{
                    backgroundColor: theme.palette.background.paper,
                    borderRadius: "5px",
                    textAlign: "center", // Align content (including the image) to center
                    marginTop: "30px",
                    paddingTop: "20px",
                    minHeight: "calc(100vh - 64px)", // Subtract header height
                }}
            >
                <Typography variant="h2" gutterBottom>
                    Terms of Service
                </Typography>
                <Typography variant="body1" paragraph>
                    Welcome to AboutCSGO!
                </Typography>
                <Typography variant="body1" paragraph>
                    These terms and conditions outline the rules and regulations
                    for the use of AboutCSGO's Website, located at
                    aboutcsgo.com.
                </Typography>
                <Typography variant="body1" paragraph>
                    By accessing this website we assume you accept these terms
                    and conditions. Do not continue to use AboutCSGO if you do
                    not agree to take all of the terms and conditions stated on
                    this page.
                </Typography>
                <Typography variant="body1" paragraph>
                    The following terminology applies to these Terms and
                    Conditions, Privacy Statement and Disclaimer Notice and all
                    Agreements: "Client", "You" and "Your" refers to you, the
                    person log on this website and compliant to the Company's
                    terms and conditions. "The Company", "Ourselves", "We",
                    "Our" and "Us", refers to our Company. "Party", "Parties",
                    or "Us", refers to both the Client and ourselves. All terms
                    refer to the offer, acceptance and consideration of payment
                    necessary to undertake the process of our assistance to the
                    Client in the most appropriate manner for the express
                    purpose of meeting the Client's needs in respect of
                    provision of the Company's stated services, in accordance
                    with and subject to, prevailing law of Netherlands. Any use
                    of the above terminology or other words in the singular,
                    plural, capitalization and/or he/she or they, are taken as
                    interchangeable and therefore as referring to same.
                </Typography>
                <Typography variant="body1" paragraph>
                    Cookies: We employ the use of cookies. By accessing
                    AboutCSGO, you agreed to use cookies in agreement with
                    AboutCSGO's Privacy Policy. Most interactive websites use
                    cookies to let us retrieve the user's details for each
                    visit. Cookies are used by our website to enable the
                    functionality of certain areas to make it easier for people
                    visiting our website. Some of our affiliate/advertising
                    partners may also use cookies.
                </Typography>
                <Typography variant="body1" paragraph>
                    License: Unless otherwise stated, AboutCSGO and/or its
                    licensors own the intellectual property rights for all
                    material on AboutCSGO. All intellectual property rights are
                    reserved. You may access this from AboutCSGO for your own
                    personal use subjected to restrictions set in these terms
                    and conditions.
                </Typography>
                {/* Add more content as needed */}
            </Container>
        </>
    );
};

export default TermsOfService;
