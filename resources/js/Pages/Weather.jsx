import {router, usePage} from "@inertiajs/react";
import {useState} from "react";

export default function Weather() {
    const { weather, forecast, city } = usePage().props;
    const { current } = weather;

    const [selectedCity, setSelectedCity] = useState(city);

    const handleCityChange = (event) => {
        const newCity = event?.target?.value;
        setSelectedCity(newCity)
        router.get(`/weather?city=${newCity}`)
    }

    const cities = [
        "London",
        "New York",
        "Paris",
        "Tokyo",
        "Moscow",
        "Berlin",
        "Beijing",
        "Sydney",
        "Dubai",
        "Los Angeles",
        "Baku"
    ];

    return (
        <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-700 p-6">
            <div className="bg-white p-8 rounded-2xl shadow-lg max-w-md w-full">
                <select name="" id=""
                        className="mb-4 p-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                        value={selectedCity}
                        onChange={handleCityChange}
                >
                    {cities.map(city => (
                        <option value={city} key={city}>{city}</option>
                    ))}
                </select>
                <div className="max-w-md w-full bg-white shadow-lg rounded-xl p-6">
                    <h1 className="text-3xl font-bold text-gray-800 text-center">
                        {city}
                    </h1>

                    <div className="mt-4 flex flex-col items-center">
                        <p className="text-5xl font-semibold text-gray-900">
                            {current?.temp_c}°C
                        </p>
                        <p className="text-lg text-gray-600 mt-1">
                            UV Index: {current?.uv}
                        </p>
                    </div>

                    <h2 className="mt-6 text-xl font-semibold text-gray-800 border-b pb-2">
                        Forecast for 3 Days:
                    </h2>

                    <div className="mt-4 space-y-4">
                        {forecast["forecast"]["forecastday"].map(({ date, day }) => (
                            <div key={date} className="p-4 bg-gray-100 rounded-lg shadow-sm">
                                <p className="text-md font-semibold">{new Date(date).toLocaleDateString("en-US", { weekday: "long", day: "2-digit", month: "short" })}</p>
                                <div className="flex justify-between mt-2 text-lg">
                                    <div className="text-blue-500 font-semibold">🌞 {day.maxtemp_c}°C</div>
                                    <div className="text-gray-700 font-semibold">🌙 {day.mintemp_c}°C</div>
                                </div>
                                <p className="text-sm text-gray-500">UV Index: {day.uv}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
