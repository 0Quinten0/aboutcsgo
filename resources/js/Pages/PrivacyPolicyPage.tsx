import React from "react";
import useHelmet from "../hooks/useHelmet";
import { Typography, Container, useTheme } from "@mui/material";
import { Head } from "@inertiajs/react";

const PrivacyPolicy = () => {
    const helmet = useHelmet();

    const theme = useTheme();

    return (
        <>
            <Head>
                <title>Privacy Policy | AboutCSGO</title>
                <meta
                    head-key="description"
                    name="description"
                    content={`The privacy policy of AboutCSGO`}
                />
                <link
                    head-key="canonical"
                    rel="canonical"
                    href={`https://www.aboutcsgo.com/privacy-policy`}
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
                    Privacy Policy
                </Typography>
                <Typography variant="body1" paragraph>
                    At AboutCSGO, we are committed to protecting your privacy.
                    This Privacy Policy outlines how we collect, use, and
                    disclose your personal information when you use our website.
                </Typography>
                <Typography variant="body1" paragraph>
                    When you log in to AboutCSGO using Steam OpenID, we collect
                    and store basic information from your Steam profile,
                    including your steam_id, nickname, profile URL, and avatar.
                    This information is stored in our database to enhance your
                    user experience on our platform.
                </Typography>
                <Typography variant="body1" paragraph>
                    Please note that the data we collect from your Steam profile
                    is considered public data since it can be accessed from your
                    public Steam profile. We do not collect any sensitive
                    information beyond what is publicly available on Steam.
                </Typography>
                <Typography variant="body1" paragraph>
                    In addition to Steam profile data, we also store user
                    actions such as votes and other interactions with our
                    platform. This data helps us improve our services and tailor
                    the user experience to your needs.
                </Typography>
                <Typography variant="body1" paragraph>
                    We are committed to protecting your personal information and
                    ensuring its confidentiality. We do not sell, trade, or
                    otherwise transfer your personal information to third
                    parties without your consent.
                </Typography>
                {/* Add more content as needed */}
            </Container>
        </>
    );
};

export default PrivacyPolicy;
