import { SearchOutlined, TagsTwoTone } from '@ant-design/icons'
import { Col, Input, Row } from 'antd'
import React, { useEffect, useState } from 'react'
import { Colors } from '../utils/Colors'

export default function Category() {
    const [datas, setDatas] = useState([]);

    useEffect(() => {
        setDatas(['APPAREL', 'KIDS APPAREL', 'LINEN', 'OTHERS']);
    }, []);

    return (
        <Row gutter={12} align={'middle'} style={{ marginBottom: 12 }}>
            <Col xs={24} sm={9}>
                <Input placeholder='Cari produk' prefix={<SearchOutlined />} />
            </Col>
            <Col xs={24} sm={15}>
                <div style={{ overflowX: 'auto', whiteSpace: 'nowrap' }}>
                    <div style={{ display: 'flex', gap: 12, width: 'max-content' }}>
                        {
                            datas.length > 0 &&
                            datas.map((val, i) => (
                                <button key={i} type='button' style={{
                                    border: 0,
                                    backgroundColor: Colors.primary,
                                    color: 'white',
                                    paddingTop: 4,
                                    paddingBottom: 4,
                                    paddingLeft: 12,
                                    paddingRight: 12,
                                    borderRadius: 6,
                                    textTransform: 'uppercase',
                                    cursor: 'pointer',
                                    flexShrink: 0, // <- penting juga!
                                }}>
                                    <TagsTwoTone /> {val}
                                </button>
                            ))
                        }
                    </div>
                </div>

            </Col>
        </Row>
    )
}
