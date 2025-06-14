import { FullscreenExitOutlined, FullscreenOutlined, LeftCircleTwoTone } from '@ant-design/icons'
import { Col, Row } from 'antd'
import React, { useState } from 'react'
import { Colors } from '../utils/Colors'

export default function Header() {
    const [isFullscreen, setIsFullscreen] = useState(false);

    const handleToggleFullscreen = async () => {
        const elem = document.documentElement;

        try {
            if (!document.fullscreenElement) {
                await elem.requestFullscreen();
                setIsFullscreen(true);
            } else {
                await document.exitFullscreen();
                setIsFullscreen(false);
            }
        } catch (err) {
            console.error("Fullscreen error:", err);
        }
    };

    const handleToDashboard = () => {
        const baseUrl = import.meta.env.VITE_APP_URL;
        window.location.href = baseUrl;
    }


    return (
        <Row style={{ marginBottom: 24 }}>
            <Col span={24} style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between' }}>
                <div>
                    <div style={{ fontSize: 24, fontWeight: 'bold', width: '100%' }}>Kios Oxxo Laundry</div>
                    <div style={{ fontSize: 14 }}>Jl. Merdekan raya no. 666 Surabaya</div>
                </div>
                <div style={{ display: 'flex', gap: 6, marginTop: 12 }}>
                    <button
                        onClick={handleToDashboard}
                        type='button'
                        style={{
                            borderWidth: 1,
                            borderStyle: 'solid',
                            borderColor: Colors.primary,
                            height: 30, width: 30,
                            backgroundColor: Colors.blue100,
                            borderRadius: 4,
                            cursor: 'pointer'
                        }}>
                        <LeftCircleTwoTone />
                    </button>

                    <button onClick={handleToggleFullscreen} type='button'
                        style={{
                            borderWidth: 1,
                            borderStyle: 'solid',
                            borderColor: isFullscreen ? Colors.red : Colors.primary,
                            height: 30, width: 30,
                            backgroundColor: isFullscreen ? Colors.red100 : Colors.blue100,
                            borderRadius: 4,
                            cursor: 'pointer'
                        }}>
                        {
                            isFullscreen
                                ? <FullscreenExitOutlined style={{ color: Colors.red800 }} />
                                : <FullscreenOutlined style={{ color: Colors.primary }} />
                        }

                    </button>
                </div>
            </Col>
        </Row>
    )
}
