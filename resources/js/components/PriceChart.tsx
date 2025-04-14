import React, { useState, useEffect } from "react";
import { LineChart, ChartsXAxisProps } from "@mui/x-charts";
import {
    Typography,
    Paper,
    Button,
    ButtonGroup,
    Box,
    useTheme,
} from "@mui/material";
import { CSSProperties } from "react";
import { AiOutlineArrowUp, AiOutlineArrowDown } from "react-icons/ai";
interface PriceChartProps {
    historicalPrices: { [key: string]: any } | undefined;
    selectedExterior: string;
    priceType: string;
}

const PriceChart: React.FC<PriceChartProps> = ({
    historicalPrices,
    selectedExterior,
    priceType,
}) => {
    const [chartData, setChartData] = useState<
        {
            time: Date;
            avgPrice?: number;
            lowestPrice?: number;
        }[]
    >([]);
    const [selectedTimeRange, setSelectedTimeRange] = useState<
        "7d" | "1m" | "3m" | "6m" | "1y" | "all"
    >("1y");
    const [priceChanges, setPriceChanges] = useState<{
        "1d": number | null;
        "1m": number | null;
        "3m": number | null;
        "6m": number | null;
        "1y": number | null;
        all: number | null;
    }>({
        "1d": null,
        "1m": null,
        "3m": null,
        "6m": null,
        "1y": null,
        all: null,
    });
    const theme = useTheme();

    const getButtonStyle = (range: string): CSSProperties => {
        const isSelected = selectedTimeRange === range;
        const baseStyle: CSSProperties = {
            backgroundColor: isSelected
                ? theme.palette.primary.main
                : "transparent",
            color: isSelected
                ? theme.palette.text.secondary
                : theme.palette.getContrastText(theme.palette.primary.main),
            border: "none",
            padding: "0px",
            borderRadius: "4px",
            cursor: "pointer",
            fontSize: "0.875rem",
            minWidth: "32px",
            transition: "background-color 0.3s",
            fontWeight: 500,
            textTransform: "uppercase" as const,
        };

        const hoverStyle: CSSProperties = {
            backgroundColor: isSelected
                ? theme.palette.primary.main
                : theme.palette.action.hover,
        };

        return {
            ...baseStyle,
            ...(isSelected ? hoverStyle : {}),
        };
    };

    useEffect(() => {
        try {
            if (
                !historicalPrices ||
                !historicalPrices[priceType] ||
                !historicalPrices[priceType][selectedExterior] ||
                !historicalPrices[priceType][selectedExterior].daily
            ) {
                setChartData([]);
                setPriceChanges({
                    "1d": null,
                    "1m": null,
                    "3m": null,
                    "6m": null,
                    "1y": null,
                    all: null,
                });
                return;
            }

            const dailyData =
                historicalPrices[priceType][selectedExterior].daily;
            const currentDate = new Date();
            let filteredData: {
                time: Date;
                avgPrice?: number;
                lowestPrice?: number;
            }[] = [];

            switch (selectedTimeRange) {
                case "7d":
                    filteredData = dailyData
                        .filter((item: any) => {
                            const itemDate = new Date(item.day);
                            const sevenDaysAgo = new Date(currentDate);
                            sevenDaysAgo.setDate(currentDate.getDate() - 7);
                            return itemDate >= sevenDaysAgo;
                        })
                        .map((item: any) => ({
                            time: new Date(item.day),
                            avgPrice: parseFloat(item.avg_price),
                            lowestPrice: parseFloat(item.lowest_price),
                        }));
                    break;
                case "1m":
                    filteredData = dailyData
                        .filter((item: any) => {
                            const itemDate = new Date(item.day);
                            const oneMonthAgo = new Date(currentDate);
                            oneMonthAgo.setMonth(currentDate.getMonth() - 1);
                            return itemDate >= oneMonthAgo;
                        })
                        .map((item: any) => ({
                            time: new Date(item.day),
                            avgPrice: parseFloat(item.avg_price),
                            lowestPrice: parseFloat(item.lowest_price),
                        }));
                    break;
                case "3m":
                    filteredData = dailyData
                        .filter((item: any) => {
                            const itemDate = new Date(item.day);
                            const threeMonthsAgo = new Date(currentDate);
                            threeMonthsAgo.setMonth(currentDate.getMonth() - 3);
                            return itemDate >= threeMonthsAgo;
                        })
                        .map((item: any) => ({
                            time: new Date(item.day),
                            avgPrice: parseFloat(item.avg_price),
                            lowestPrice: parseFloat(item.lowest_price),
                        }));
                    break;
                case "6m":
                    filteredData = dailyData
                        .filter((item: any) => {
                            const itemDate = new Date(item.day);
                            const sixMonthsAgo = new Date(currentDate);
                            sixMonthsAgo.setMonth(currentDate.getMonth() - 6);
                            return itemDate >= sixMonthsAgo;
                        })
                        .map((item: any) => ({
                            time: new Date(item.day),
                            avgPrice: parseFloat(item.avg_price),
                            lowestPrice: parseFloat(item.lowest_price),
                        }));
                    break;
                case "1y":
                    filteredData = dailyData
                        .filter((item: any) => {
                            const itemDate = new Date(item.day);
                            const oneYearAgo = new Date(currentDate);
                            oneYearAgo.setFullYear(
                                currentDate.getFullYear() - 1,
                            );
                            return itemDate >= oneYearAgo;
                        })
                        .map((item: any) => ({
                            time: new Date(item.day),
                            avgPrice: parseFloat(item.avg_price),
                            lowestPrice: parseFloat(item.lowest_price),
                        }));
                    break;
                case "all":
                default:
                    filteredData = dailyData.map((item: any) => ({
                        time: new Date(item.day),
                        lowestPrice: parseFloat(item.lowest_price),
                        avgPrice: parseFloat(item.avg_price),
                    }));
                    break;
            }

            filteredData.sort((a, b) => a.time.getTime() - b.time.getTime());
            setChartData(filteredData);

            // Calculate price changes
            const latestPrice =
                filteredData.length > 0
                    ? filteredData[filteredData.length - 1].lowestPrice
                    : null;

            const priceChanges: {
                "1d": number | null;
                "1m": number | null;
                "3m": number | null;
                "6m": number | null;
                "1y": number | null;
                all: number | null;
            } = {
                "1d": null,
                "1m": null,
                "3m": null,
                "6m": null,
                "1y": null,
                all: null,
            };

            if (latestPrice !== null && latestPrice !== undefined) {
                const oneDayAgo = new Date(currentDate);
                oneDayAgo.setDate(currentDate.getDate() - 1);
                const oneDayData = filteredData.find(
                    (item) =>
                        item.time.getFullYear() === oneDayAgo.getFullYear() &&
                        item.time.getMonth() === oneDayAgo.getMonth() &&
                        item.time.getDate() === oneDayAgo.getDate(),
                );
                priceChanges["1d"] =
                    oneDayData?.lowestPrice !== undefined
                        ? ((latestPrice - oneDayData!.lowestPrice!) /
                              oneDayData!.lowestPrice!) *
                          100
                        : null;

                const oneMonthAgo = new Date(currentDate);
                oneMonthAgo.setMonth(currentDate.getMonth() - 1);
                const oneMonthData = filteredData.find(
                    (item) => item.time <= oneMonthAgo,
                );
                priceChanges["1m"] =
                    oneMonthData?.lowestPrice !== undefined
                        ? ((latestPrice - oneMonthData!.lowestPrice!) /
                              oneMonthData!.lowestPrice!) *
                          100
                        : null;

                const threeMonthsAgo = new Date(currentDate);
                threeMonthsAgo.setMonth(currentDate.getMonth() - 3);
                const threeMonthsData = filteredData.find(
                    (item) => item.time <= threeMonthsAgo,
                );
                priceChanges["3m"] =
                    threeMonthsData?.lowestPrice !== undefined
                        ? ((latestPrice - threeMonthsData!.lowestPrice!) /
                              threeMonthsData!.lowestPrice!) *
                          100
                        : null;

                const sixMonthsAgo = new Date(currentDate);
                sixMonthsAgo.setMonth(currentDate.getMonth() - 6);
                const sixMonthsData = filteredData.find(
                    (item) => item.time <= sixMonthsAgo,
                );
                priceChanges["6m"] =
                    latestPrice !== null &&
                    sixMonthsData?.lowestPrice !== undefined
                        ? ((latestPrice - sixMonthsData!.lowestPrice!) /
                              sixMonthsData!.lowestPrice!) *
                          100
                        : null;

                const oneYearAgo = new Date(currentDate);
                oneYearAgo.setFullYear(currentDate.getFullYear() - 1);
                const oneYearData = filteredData.find(
                    (item) => item.time <= oneYearAgo,
                );
                priceChanges["1y"] =
                    latestPrice !== null &&
                    oneYearData?.lowestPrice !== undefined
                        ? ((latestPrice - oneYearData!.lowestPrice!) /
                              oneYearData!.lowestPrice!) *
                          100
                        : null;

                const allTimeData =
                    filteredData.length > 0 ? filteredData[0] : null;

                priceChanges["all"] =
                    latestPrice !== null &&
                    allTimeData?.lowestPrice !== undefined
                        ? ((latestPrice - allTimeData!.lowestPrice!) /
                              allTimeData!.lowestPrice!) *
                          100
                        : null;
            }
            setPriceChanges(priceChanges);
        } catch (error) {
            console.error("Error in chart processing:", error);
        }
    }, [historicalPrices, selectedExterior, priceType, selectedTimeRange]);

    const getDateFormat = (timeRange: string) => {
        switch (timeRange) {
            case "7d":
                return (value: Date) =>
                    value.toLocaleDateString(undefined, {
                        month: "short",
                        day: "numeric",
                    });
            case "1m":
                return (value: Date) =>
                    value.toLocaleDateString(undefined, {
                        month: "short",
                        day: "numeric",
                    });
            case "3m":
                return (value: Date) =>
                    value.toLocaleDateString(undefined, {
                        month: "short",
                        day: "numeric",
                    });
            case "6m":
                return (value: Date) =>
                    value.toLocaleDateString(undefined, {
                        month: "short",
                        day: "numeric",
                    });
            case "1y":
            case "all":
            default:
                return (value: Date) =>
                    value.toLocaleDateString(undefined, {
                        month: "short",
                        year: "numeric",
                    });
        }
    };

    const getTicks = (timeRange: string) => {
        switch (timeRange) {
            case "7d":
                return 7;
            case "1m":
                return new Date(
                    new Date().getFullYear(),
                    new Date().getMonth() + 1,
                    0,
                ).getDate();
            case "3m":
                return 3; // Reduced ticks for better readability over 3 months
            case "6m":
                return 6; // Reduced ticks for better readability over 6 months
            case "1y":
                return 12;
            case "all":
            default:
                return undefined;
        }
    };

    return (
        <>
            {" "}
            <Paper elevation={3} style={{ padding: "10px" }}>
                <Typography variant="body1">Price Changes</Typography>

                <Box
                    sx={{
                        display: "flex",
                        flexDirection: "row",
                        justifyContent: "space-between",
                        alignItems: "center",
                        marginTop: "10px",
                        marginBottom: "20px", // Add some margin below the price changes
                        flexWrap: "wrap",
                    }}
                >
                    {Object.entries(priceChanges).map(([key, value]) => {
                        if (value === null) {
                            return null; // Don't render if value is null
                        }
                        return (
                            <Box
                                key={key}
                                sx={{
                                    display: "flex",
                                    flexDirection: "column",
                                    alignItems: "center",
                                    margin: "4px 8px", // Add some margin around each box
                                }}
                            >
                                <Typography variant="caption" display="block">
                                    {key.toUpperCase()}
                                </Typography>
                                <Box
                                    sx={{
                                        display: "flex",
                                        alignItems: "center",
                                    }}
                                >
                                    {/* {value > 0 ? (
                                        // <AiOutlineArrowUp />
                                    ) : (
                                        // <AiOutlineArrowDown />
                                    )} */}
                                    <Typography
                                        variant="body2"
                                        style={{
                                            color: value > 0 ? "green" : "red",
                                            fontWeight: 500,
                                        }}
                                    >
                                        {value ? value.toFixed(2) + "%" : "N/A"}
                                    </Typography>
                                </Box>
                            </Box>
                        );
                    })}
                </Box>
            </Paper>
            <Paper elevation={3} style={{ padding: "10px", marginTop: "20px" }}>
                <Box
                    style={{
                        display: "flex",
                        flexDirection: "column",
                        alignItems: "flex-start",
                        width: "100%",
                        height: 300,
                    }}
                >
                    <ButtonGroup
                        size="small"
                        variant="text"
                        aria-label="Time range filter"
                        style={{ marginBottom: "10px" }}
                    >
                        <Button
                            style={getButtonStyle("7d")}
                            onClick={() => {
                                setSelectedTimeRange("7d");
                            }}
                        >
                            7D
                        </Button>
                        <Button
                            style={getButtonStyle("1m")}
                            onClick={() => {
                                setSelectedTimeRange("1m");
                            }}
                        >
                            1M
                        </Button>
                        <Button
                            style={getButtonStyle("3m")}
                            onClick={() => {
                                setSelectedTimeRange("3m");
                            }}
                        >
                            3M
                        </Button>
                        <Button
                            style={getButtonStyle("6m")}
                            onClick={() => {
                                setSelectedTimeRange("6m");
                            }}
                        >
                            6M
                        </Button>
                        <Button
                            style={getButtonStyle("1y")}
                            onClick={() => {
                                setSelectedTimeRange("1y");
                            }}
                        >
                            1Y
                        </Button>
                        <Button
                            style={getButtonStyle("all")}
                            onClick={() => {
                                setSelectedTimeRange("all");
                            }}
                        >
                            All
                        </Button>
                    </ButtonGroup>
                    <LineChart
                        dataset={chartData}
                        xAxis={[
                            {
                                dataKey: "time",
                                valueFormatter:
                                    getDateFormat(selectedTimeRange),
                                scaleType: "time",
                                ticks: getTicks(selectedTimeRange) as number,
                            } as ChartsXAxisProps,
                        ]}
                        rightAxis={{}}
                        leftAxis={null}
                        series={[
                            {
                                dataKey: "lowestPrice",
                                label: "Lowest Price",
                                showMark: false,
                            },
                            {
                                dataKey: "avgPrice",
                                label: "Average Price",
                                showMark: false,
                                color: "transparent",
                            },
                        ]}
                        slotProps={{ legend: { hidden: true } }}
                        margin={{
                            left: 20,
                            right: 30,
                            top: 10,
                            bottom: 50,
                        }}
                    ></LineChart>
                </Box>
            </Paper>
        </>
    );
};

export default PriceChart;
